/* Harmony Wellness Center — UI interactions */
(function () {
    'use strict';

    var body = document.body;
    var base = (body.dataset.base || '').replace(/\/+$/, '');

    /* ---------- Mobile navigation drawer ---------- */
    var navToggle = document.getElementById('navToggle');
    var navBackdrop = document.getElementById('navBackdrop');
    var mainNav = document.getElementById('mainNav');

    function setNavOpen(open) {
        body.classList.toggle('nav-open', open);
        if (navToggle) {
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            navToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
        }
    }

    if (navToggle) {
        navToggle.addEventListener('click', function () {
            setNavOpen(!body.classList.contains('nav-open'));
        });
    }
    if (navBackdrop) {
        navBackdrop.addEventListener('click', function () { setNavOpen(false); });
    }
    if (mainNav) {
        mainNav.addEventListener('click', function (e) {
            if (e.target.closest('a:not(.dropdown-toggle)')) setNavOpen(false);
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') setNavOpen(false);
    });

    /* ---------- Dropdown (mobile tap / desktop focus) ---------- */
    var dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(function (dd) {
        var toggle = dd.querySelector('.dropdown-toggle');
        if (!toggle) return;
        toggle.addEventListener('click', function (e) {
            if (window.innerWidth <= 1240) {
                e.preventDefault();
                dd.classList.toggle('open');
                toggle.setAttribute('aria-expanded', dd.classList.contains('open') ? 'true' : 'false');
            }
        });
    });

    /* ---------- Sticky header shadow ---------- */
    var header = document.getElementById('siteHeader');
    function onScroll() {
        if (header) header.classList.toggle('scrolled', window.scrollY > 8);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---------- Active navigation link ---------- */
    var basePattern = (base || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    var path = (location.pathname || '/').replace(new RegExp('^' + basePattern), '') || '/';
    path = '/' + path.replace(/^\/+/, '').replace(/\/+$/, '') || '/';
    if (path !== '/') path = path.replace(/\/$/, '');

    document.querySelectorAll('.main-nav a[href]').forEach(function (link) {
        if (link.classList.contains('nav-cta')) return;
        var href = link.getAttribute('href') || '';
        var hrefParts = href.split('#');
        var target = hrefParts[0].split('?')[0].replace(base, '') || '/';
        target = target === '' ? '/' : target;
        var hash = hrefParts[1] || '';
        if (link.classList.contains('dropdown-toggle')) {
            // A dropdown toggle is active on its own page or any page under it
            // (e.g. /physiotherapy/electrotherapy keeps the Physiotherapy toggle active).
            var isOwnSection = path.indexOf(target + '/') === 0;
            if (path === target || isOwnSection) {
                link.classList.add('active');
                var dd = link.closest('.dropdown');
                if (dd) dd.classList.add('open');
            }
        } else if (target === path && (hash === '' || hash === location.hash.slice(1))) {
            link.classList.add('active');
        }
    });

    /* ---------- Scroll reveal ---------- */
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var revealEls = document.querySelectorAll('.reveal');

    if (reduceMotion || !('IntersectionObserver' in window)) {
        revealEls.forEach(function (el) { el.classList.add('revealed'); });
    } else {
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(function (el) { observer.observe(el); });
    }

    /* ---------- Photo card carousel (e.g. Why Choose Us) ---------- */
    var carousels = document.querySelectorAll('[data-carousel]');
    var reduceMotionCarousel = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var CAROUSEL_GAP = 24; // px — must match the .carousel-card margin-right in style.css

    carousels.forEach(function (carousel) {
        var track = carousel.querySelector('.carousel-track');
        var viewport = carousel.querySelector('.carousel-viewport');
        var prevBtn = carousel.querySelector('.carousel-prev');
        var nextBtn = carousel.querySelector('.carousel-next');
        var dotsWrap = carousel.querySelector('.carousel-dots');
        if (!track || !viewport) return;

        var cards = Array.prototype.slice.call(track.children);
        var perView = 1;
        var index = 0;
        var timer = null;

        function cardsPerView() {
            var w = viewport.clientWidth;
            if (w >= 1024) return 3;
            if (w >= 620) return 2;
            return 1;
        }

        function maxIndex() {
            return Math.max(0, cards.length - perView);
        }

        function slideOffset() {
            return (cards[0] ? cards[0].offsetWidth : 0) + CAROUSEL_GAP;
        }

        function goTo(i, instant) {
            index = Math.min(Math.max(0, i), maxIndex());
            if (instant) track.style.transition = 'none';
            track.style.transform = 'translateX(' + (-(index * slideOffset())) + 'px)';
            if (instant) {
                // force a reflow so the transition is truly skipped
                void track.offsetWidth;
                track.style.transition = '';
            }
            updateDots();
        }

        function next() { goTo(index >= maxIndex() ? 0 : index + 1); }
        function prev() { goTo(index <= 0 ? maxIndex() : index - 1); }

        // Only the positions the user can actually land on get a dot
        // (e.g. 5 cards shown 3-at-a-time = 3 dots), avoiding dead dots.
        function dotCount() {
            return Math.min(cards.length, Math.max(1, cards.length - perView + 1));
        }

        function buildDots() {
            if (!dotsWrap) return;
            dotsWrap.innerHTML = '';
            for (var i = 0; i < dotCount(); i++) {
                (function (pos) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'carousel-dot';
                    dot.setAttribute('role', 'tab');
                    dot.setAttribute('aria-label', 'Go to position ' + (pos + 1));
                    dot.addEventListener('click', function () { goTo(pos); });
                    dotsWrap.appendChild(dot);
                })(i);
            }
        }

        function updateDots() {
            if (!dotsWrap) return;
            var dots = dotsWrap.children;
            for (var d = 0; d < dots.length; d++) {
                dots[d].classList.toggle('active', d === index);
                dots[d].setAttribute('aria-selected', d === index ? 'true' : 'false');
            }
        }

        function startAuto() {
            stopAuto();
            if (!reduceMotionCarousel && cards.length > perView) {
                timer = setInterval(next, 4500);
            }
        }
        function stopAuto() {
            if (timer) { clearInterval(timer); timer = null; }
        }

        function applyCardWidth() {
            cards.forEach(function (card) {
                card.style.flexBasis = (100 / perView) + '%';
            });
        }

        function init() {
            perView = cardsPerView();
            applyCardWidth();
            carousel.classList.add('is-carousel');

            buildDots();
            // Manual navigation moves the carousel but does not restart the
            // timer — autoplay resumes when the mouse leaves / focus is lost.
            if (prevBtn) prevBtn.addEventListener('click', prev);
            if (nextBtn) nextBtn.addEventListener('click', next);

            // Pause while the visitor is interacting with the carousel
            carousel.addEventListener('mouseenter', stopAuto);
            carousel.addEventListener('mouseleave', startAuto);
            carousel.addEventListener('focusin', stopAuto);
            carousel.addEventListener('focusout', startAuto);

            goTo(0, true);
            startAuto();
        }

        var resizeT;
        window.addEventListener('resize', function () {
            clearTimeout(resizeT);
            resizeT = setTimeout(function () {
                var newPerView = cardsPerView();
                if (newPerView !== perView) {
                    perView = newPerView;
                    applyCardWidth();
                    buildDots();
                    goTo(index, true);
                    startAuto();
                }
            }, 150);
        });

        init();
    });

    /* ---------- Admin: confirm before delete ---------- */
    document.querySelectorAll('form.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!window.confirm('Delete this item? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    /* ---------- Footer year ---------- */
    document.querySelectorAll('[data-year]').forEach(function (el) {
        el.textContent = String(new Date().getFullYear());
    });

    /* ---------- Lightbox: click a gallery photo to view it large ---------- */
    var lightboxTriggers = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
    var lightboxEl = null;
    var lightboxImg = null;
    var lightboxIndex = 0;
    var lightboxFocusBefore = null;
    var lightboxItems = [];
    var reducedMotionLb = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function lightboxBuild() {
        if (lightboxEl) return lightboxEl;
        lightboxEl = document.createElement('div');
        lightboxEl.className = 'lightbox';
        lightboxEl.setAttribute('role', 'dialog');
        lightboxEl.setAttribute('aria-modal', 'true');
        lightboxEl.setAttribute('aria-label', 'Photo viewer');
        lightboxEl.innerHTML =
            '<button type="button" class="lightbox-btn lightbox-close" aria-label="Close photo viewer">' +
            '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>' +
            '<button type="button" class="lightbox-btn lightbox-prev" aria-label="Previous photo">' +
            '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></button>' +
            '<button type="button" class="lightbox-btn lightbox-next" aria-label="Next photo">' +
            '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>' +
            '<figure class="lightbox-figure">' +
            '<img class="lightbox-img" alt="">' +
            '<figcaption class="lightbox-caption"><h3></h3><p></p><div class="lightbox-count"></div></figcaption>' +
            '</figure>';
        document.body.appendChild(lightboxEl);
        lightboxImg = lightboxEl.querySelector('.lightbox-img');
        return lightboxEl;
    }

    function lightboxRender() {
        if (!lightboxItems.length) return;
        var item = lightboxItems[lightboxIndex];
        lightboxImg.classList.remove('is-loaded');
        // Swap src then fade in once the new photo has decoded.
        lightboxImg.onload = function () { lightboxImg.classList.add('is-loaded'); };
        if (lightboxImg.src === item.src && lightboxImg.complete) {
            // Same cached photo re-selected: no load event will fire.
            lightboxImg.classList.add('is-loaded');
        } else {
            lightboxImg.src = item.src;
        }
        lightboxImg.alt = item.alt || '';
        var cap = lightboxEl.querySelector('.lightbox-caption');
        cap.querySelector('h3').textContent = item.title || '';
        cap.querySelector('p').textContent = item.description || '';
        cap.querySelector('.lightbox-count').textContent =
            (lightboxIndex + 1) + ' / ' + lightboxItems.length;
        var prev = lightboxEl.querySelector('.lightbox-prev');
        var next = lightboxEl.querySelector('.lightbox-next');
        prev.disabled = lightboxItems.length < 2;
        next.disabled = lightboxItems.length < 2;
        if (reducedMotionLb) lightboxImg.classList.add('is-loaded');
    }

    function lightboxOpen(trigger) {
        if (!trigger) return;
        var items = lightboxTriggers.map(function (t) {
            return {
                src: (t.querySelector('.media-thumb img') || {}).getAttribute('src') || '',
                alt: (t.querySelector('.media-thumb img') || {}).getAttribute('alt') || '',
                title: (t.closest('.media-card') ? t.closest('.media-card').querySelector('.media-body h3') : null)
                    ? t.closest('.media-card').querySelector('.media-body h3').textContent : '',
                description: (t.closest('.media-card') ? t.closest('.media-card').querySelector('.media-body p') : null)
                    ? t.closest('.media-card').querySelector('.media-body p').textContent : ''
            };
        });
        if (!items.length) return;
        lightboxItems = items;
        lightboxIndex = Math.max(0, lightboxTriggers.indexOf(trigger));
        lightboxFocusBefore = document.activeElement;
        lightboxBuild();
        lightboxRender();
        lightboxEl.classList.add('is-open');
        lightboxEl.setAttribute('aria-hidden', 'false');
        document.body.classList.add('lightbox-locked');
        var closeBtn = lightboxEl.querySelector('.lightbox-close');
        window.setTimeout(function () { closeBtn.focus(); }, reducedMotionLb ? 0 : 60);
    }

    function lightboxClose() {
        if (!lightboxEl || !lightboxEl.classList.contains('is-open')) return;
        lightboxEl.classList.remove('is-open');
        lightboxEl.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('lightbox-locked');
        if (lightboxFocusBefore && lightboxFocusBefore.focus) lightboxFocusBefore.focus();
    }

    function lightboxStep(dir) {
        if (!lightboxItems.length) return;
        lightboxIndex = (lightboxIndex + dir + lightboxItems.length) % lightboxItems.length;
        lightboxRender();
    }

    if (lightboxTriggers.length) {
        lightboxTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                lightboxOpen(trigger);
            });
        });
        lightboxBuild().addEventListener('click', function (e) {
            var target = e.target;
            if (target.closest('.lightbox-btn')) return; // buttons handle themselves
            if (target === lightboxEl) lightboxClose(); // click outside the photo
        });
        lightboxEl.addEventListener('click', function (e) {
            var btn = e.target.closest('.lightbox-btn');
            if (!btn) return;
            if (btn.classList.contains('lightbox-close')) lightboxClose();
            else if (btn.classList.contains('lightbox-prev')) lightboxStep(-1);
            else if (btn.classList.contains('lightbox-next')) lightboxStep(1);
        });
        document.addEventListener('keydown', function (e) {
            if (!lightboxEl || !lightboxEl.classList.contains('is-open')) return;
            if (e.key === 'Escape') { e.preventDefault(); lightboxClose(); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); lightboxStep(-1); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); lightboxStep(1); }
            else if (e.key === 'Tab') {
                // Keep focus inside the dialog (buttons only — the image is not focusable)
                var focusables = lightboxEl.querySelectorAll('.lightbox-btn');
                if (!focusables.length) return;
                var first = focusables[0];
                var last = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });
    }

    /* ---------- Admin idle auto-lock ---------- */
    // Only arm the lock when actually signed in (the admin layout is also
    // used by the login page, which must never show the lock screen).
    var lockEl = document.getElementById('adminLock');
    if (lockEl && body.classList.contains('admin-authed')) {
        var lockForm = document.getElementById('adminLockForm');
        var lockPassword = document.getElementById('adminLockPassword');
        var lockError = document.getElementById('adminLockError');
        var lockMsg = document.getElementById('adminLockMsg');
        var idleSeconds = parseInt(body.dataset.idleLock || '20', 10);
        if (isNaN(idleSeconds) || idleSeconds < 5) idleSeconds = 20;

        var idleTimer = null;
        var locked = false;

        function armIdle() {
            if (idleTimer) window.clearTimeout(idleTimer);
            idleTimer = window.setTimeout(lockAdmin, idleSeconds * 1000);
        }

        function lockAdmin(entry) {
            if (locked) return;
            locked = true;
            lockEl.hidden = false;
            document.body.classList.add('admin-locked');
            if (lockPassword) {
                lockPassword.value = '';
                window.setTimeout(function () { lockPassword.focus(); }, 60);
            }
            if (lockError) lockError.hidden = true;
            if (lockMsg) lockMsg.textContent = entry
                ? 'Enter your password to continue to the admin panel.'
                : 'You were idle for ' + idleSeconds + ' seconds. Enter your password to continue.';
        }

        function unlockAdmin() {
            locked = false;
            lockEl.hidden = true;
            document.body.classList.remove('admin-locked');
            armIdle();
        }

        // Any user activity resets the idle clock.
        ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'].forEach(function (evt) {
            document.addEventListener(evt, function () { if (!locked) armIdle(); }, { passive: true });
        });

        if (lockForm) {
            lockForm.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!lockPassword || !lockPassword.value) { lockPassword && lockPassword.focus(); return; }
                var btn = lockForm.querySelector('button[type="submit"]');
                if (btn) { btn.disabled = true; btn.textContent = 'Checking…'; }

                fetch(base + '/admin/unlock', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    credentials: 'same-origin',
                    body: 'csrf_token=' + encodeURIComponent(body.dataset.csrf || '') +
                          '&password=' + encodeURIComponent(lockPassword.value)
                }).then(function (res) { return res.json(); }).then(function (data) {
                    if (data && data.ok) {
                        unlockAdmin();
                    } else {
                        if (btn) { btn.disabled = false; btn.textContent = 'Unlock'; }
                        if (lockPassword) { lockPassword.value = ''; lockPassword.focus(); }
                        if (lockError) { lockError.textContent = (data && data.message) || 'Could not unlock. Try again.'; lockError.hidden = false; }
                    }
                }).catch(function () {
                    if (btn) { btn.disabled = false; btn.textContent = 'Unlock'; }
                    if (lockError) { lockError.textContent = 'Network error. Try again.'; lockError.hidden = false; }
                });
            });
        }

        // Lock on entry: landing on the panel from outside (fresh tab, typed
        // URL, or coming from the public site) shows the lock screen right
        // away, so a saved session never skips the password. Coming from
        // another admin page (or right after logging in) opens the dashboard
        // directly, and the idle lock above still applies afterwards.
        var lockOnEntry = (body.dataset.lockEntry || '0') === '1';
        var entryVisit = lockOnEntry;
        if (entryVisit && document.referrer) {
            // Coming from another admin page (or right after logging in) skips
            // the entry lock; any other referrer (typed URL, fresh tab, public
            // site, or none) keeps it armed.
            if (document.referrer.indexOf(location.origin + base + '/admin') === 0) {
                entryVisit = false;
            }
        }

        if (entryVisit) {
            lockAdmin(true);
        } else {
            armIdle();
        }
    }
})();
