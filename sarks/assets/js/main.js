/**
* Theme Name: Sarks Redesign
* Description: Main JS file with GSAP animations.
*/

(function() {
  "use strict";

  // Register GSAP ScrollTrigger
  gsap.registerPlugin(ScrollTrigger);

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim()
    if (all) {
      return [...document.querySelectorAll(el)]
    } else {
      return document.querySelector(el)
    }
  }

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all)
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener))
      } else {
        selectEl.addEventListener(type, listener)
      }
    }
  }

  /**
   * Mobile nav toggle
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('#navbar').classList.toggle('navbar-mobile')
    this.classList.toggle('bi-list')
    this.classList.toggle('bi-x')
  })

  /**
   * Navbar links active state on scroll
   */
  let navbarlinks = select('#navbar .scrollto', true)
  const navbarlinksActive = () => {
    let position = window.scrollY + 200
    navbarlinks.forEach(navbarlink => {
      if (!navbarlink.hash) return
      let section = select(navbarlink.hash)
      if (!section) return
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        navbarlink.classList.add('active')
      } else {
        navbarlink.classList.remove('active')
      }
    })
  }
  window.addEventListener('load', navbarlinksActive)
  document.addEventListener('scroll', navbarlinksActive)

  /**
   * Header Scrolled State
   */
  const headerScrolled = () => {
    if (window.scrollY > 100) {
      select('#header').classList.add('scrolled')
    } else {
      select('#header').classList.remove('scrolled')
    }
  }
  window.addEventListener('load', headerScrolled)
  document.addEventListener('scroll', headerScrolled)

  /*--------------------------------------------------------------
  # GSAP Animations
  --------------------------------------------------------------*/

  // Hero Section Animations
  const heroTimeline = gsap.timeline();

  heroTimeline
    .from('.hero-title span', {
      y: 100,
      opacity: 0,
      duration: 1,
      stagger: 0.2,
      ease: 'power4.out'
    })
    .from('.hero-subtitle', {
      y: 50,
      opacity: 0,
      duration: 1,
      ease: 'power3.out'
    }, '-=0.5');

  // Parallax Effect for Hero Video
  gsap.to('.video-bg', {
    scrollTrigger: {
      trigger: '#hero',
      start: 'top top',
      end: 'bottom top',
      scrub: true
    },
    yPercent: 50,
    ease: 'none'
  });

  // About Section Reveal
  gsap.from('.about-img', {
    scrollTrigger: {
      trigger: '.about',
      start: 'top 80%',
      end: 'bottom 20%',
      toggleActions: 'play none none reverse'
    },
    x: -100,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
  });

  gsap.from('.about-content', {
    scrollTrigger: {
      trigger: '.about',
      start: 'top 80%',
      end: 'bottom 20%',
      toggleActions: 'play none none reverse'
    },
    x: 100,
    opacity: 0,
    duration: 1,
    delay: 0.2,
    ease: 'power3.out'
  });

  // Cards Stagger Animation (Concepts & Elements)
  const animateCards = (sectionId) => {
    // Ensure elements are visible if JS fails or before animation
    gsap.set(`${sectionId} .feature-card`, { autoAlpha: 1 }); 
    
    gsap.from(`${sectionId} .feature-card`, {
      scrollTrigger: {
        trigger: sectionId,
        start: 'top 85%',
        toggleActions: 'play none none reverse'
      },
      y: 50,
      autoAlpha: 0, // Use autoAlpha for better visibility handling
      duration: 0.8,
      stagger: 0.2,
      ease: 'power3.out'
    });
  };

  animateCards('#concepts');
  animateCards('#elements');

  // Contact Form Animation
  gsap.from('.php-email-form', {
    scrollTrigger: {
      trigger: '#contact',
      start: 'top 80%',
      toggleActions: 'play none none reverse'
    },
    y: 50,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
  });

  // Parallax for Images with data-speed
  gsap.utils.toArray('[data-speed]').forEach(el => {
    gsap.to(el, {
      y: (i, target) => -100 * target.dataset.speed,
      ease: "none",
      scrollTrigger: {
        trigger: el,
        start: "top bottom",
        end: "bottom top",
        scrub: 0
      }
    });
  });

  // Background Music Control
  const bgMusic = select('#bg-music');
  const musicToggle = select('#music-toggle');
  const musicIcon = select('#music-toggle i');

  if (bgMusic && musicToggle) {
    // Try to play on load (might be blocked by browser)
    bgMusic.muted = false;
    bgMusic.volume = 0.5;
    const playPromise = bgMusic.play();

    if (playPromise !== undefined) {
      playPromise.then(_ => {
        // Autoplay started!
        musicIcon.classList.remove('bi-volume-mute-fill');
        musicIcon.classList.add('bi-volume-up-fill');
      }).catch(error => {
        // Autoplay was prevented.
        // Show muted UI.
        console.log("Autoplay prevented:", error);
        musicIcon.classList.remove('bi-volume-up-fill');
        musicIcon.classList.add('bi-volume-mute-fill');
      });
    }

    on('click', '#music-toggle', function(e) {
      if (bgMusic.paused) {
        bgMusic.play();
        musicIcon.classList.remove('bi-volume-mute-fill');
        musicIcon.classList.add('bi-volume-up-fill');
      } else {
        bgMusic.pause();
        musicIcon.classList.remove('bi-volume-up-fill');
        musicIcon.classList.add('bi-volume-mute-fill');
      }
    });
  }

})();