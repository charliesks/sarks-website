<?php
require_once __DIR__ . '/../includes/connection.php';
require_once __DIR__ . '/../includes/config.php';

/**
 * Fulfills an order by sending the product guides via email.
 */
function fulfill_order($order_group_id)
{
    global $conn;

    // 1. Fetch Order Details
    $sql = "SELECT po.*, p.pdtName FROM productsorder po 
            JOIN products p ON po.pdtId = p.pdtId 
            WHERE po.order_group_id = '$order_group_id'";
    $query = mysqli_query($conn, $sql);

    $guides_to_attach = [];
    $customer_name = "";
    $customer_email = "";
    $first_row = true;

    while ($row = mysqli_fetch_array($query)) {
        if ($first_row) {
            $customer_name = $row['ordercusname'];
            $customer_email = $row['orderemail'];
            $first_row = false;
        }

        // Prepare guide paths
        $guide_path = __DIR__ . "/../assets/guides/" . $row['pdtName'] . " Plan.pdf";
        if (file_exists($guide_path)) {
            $guides_to_attach[] = ['path' => $guide_path, 'name' => $row['pdtName'] . " Plan.pdf"];
        }
    }

    if (empty($customer_email)) {
        error_log("Fulfillment Failed: Customer email not found for order $order_group_id");
        return false;
    }

    // 3. Send Email
    if (file_exists($php_email_form = __DIR__ . '/../assets/vendor/php-email-form/php-email-form.php')) {
        include_once($php_email_form);
        $contact = new PHP_Email_Form;
        $contact->smtp = array(
            'host' => 'smtp.zoho.com',
            'username' => 'info@sarks.org',
            'password' => 'Q7aVrzHq2Lzt',
            'port' => '587'
        );

        $contact->to = $customer_email;
        $contact->from_name = 'Sarks Support';
        $contact->from_email = 'info@sarks.org';
        $contact->subject = 'Order Confirmation - Sarks';

        $contact->add_message($customer_name, 'Customer Name');
        $contact->add_message('Thank you for your order. Please find your product guides attached.', 'Message');

        foreach ($guides_to_attach as $guide) {
            $contact->add_attachment($guide['path'], $guide['name']);
        }

        $result = $contact->send();
        if ($result !== 'OK') {
            error_log("Order Confirmation Email Failed for $order_group_id: " . $result);
            return false;
        }
        return true;
    }

    error_log("Fulfillment Failed: Email library not found.");
    return false;
}
