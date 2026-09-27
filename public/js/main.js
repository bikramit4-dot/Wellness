/* Chitrawan Nature Cure Hospital — UI interactions */
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

    /* ---------- Techniques & Methods timeline: expand definition on click ---------- */
    document.querySelectorAll('.timeline-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = btn.closest('.timeline-item');
            if (!item) return;
            var open = item.classList.toggle('open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
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
        var idleSeconds = parseInt(body.dataset.idleLock || '0', 10);
        if (isNaN(idleSeconds) || idleSeconds < 0) idleSeconds = 0;

        var idleTimer = null;
        var locked = false;

        function armIdle() {
            if (idleSeconds <= 0) return; // Disabled
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

// ─── Notification bell + real-time polling ─────────────────────────
(function () {
    var btn = document.getElementById('notifBellBtn');
    var dd = document.getElementById('notifDropdown');
    var badge = document.querySelector('.notif-badge');
    var list = document.getElementById('notifList');
    var sidebarBadges = document.querySelectorAll('.sidebar-badge');
    if (!btn || !dd) return;

    var base = (document.body.getAttribute('data-base') || '').replace(/\/$/, '');
    var lastCheck = parseInt(document.body.getAttribute('data-notif-ts') || '0', 10) || Math.floor(Date.now() / 1000);
    var pollInterval = 5000; // 5 seconds
    var isFirstPoll = true;

    // ── Bell toggle ───────────────────────────────────────────────
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        dd.hidden = !dd.hidden;
    });

    document.addEventListener('click', function (e) {
        if (!dd.contains(e.target) && e.target !== btn) {
            dd.hidden = true;
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') dd.hidden = true;
    });

    // ── Sound for new notifications ────────────────────────────────
    var notifSound = null;
    function playNotifSound() {
        try {
            if (!notifSound) {
                notifSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVggoKGfmpSVX+Jk3NnSj1fdH2Cf3BUX4KFjHZoT0BfdH2Cf3BUX4KFjHZoT0BfdH2Cf3BUX4KFjHZoT0Bf');
                notifSound.volume = 0.3;
            }
            notifSound.currentTime = 0;
            notifSound.play().catch(function() {});
        } catch (e) {}
    }

    // ── Update the badge count ─────────────────────────────────────
    function setBadge(count) {
        if (count > 0) {
            if (badge) {
                badge.textContent = count;
                badge.style.display = '';
            } else {
                badge = document.createElement('span');
                badge.className = 'notif-badge';
                badge.textContent = count;
                btn.appendChild(badge);
            }
        } else if (badge) {
            badge.style.display = 'none';
        }
    }

    // ── Update sidebar badges ──────────────────────────────────────
    function updateSidebarBadges(apptNew, qrNew, reviewPending) {
        // Update appointment badge in sidebar
        var links = document.querySelectorAll('.sidebar-link');
        links.forEach(function(link) {
            var href = link.getAttribute('href') || '';
            var existingBadge = link.querySelector('.sidebar-badge');
            if (href.indexOf('/admin/appointments') !== -1) {
                if (apptNew > 0) {
                    if (existingBadge) {
                        existingBadge.textContent = apptNew;
                    } else {
                        var sp = document.createElement('span');
                        sp.className = 'sidebar-badge';
                        sp.textContent = apptNew;
                        link.appendChild(sp);
                    }
                } else if (existingBadge) {
                    existingBadge.remove();
                }
            }
            if (href.indexOf('/admin/qr-payments') !== -1) {
                if (qrNew > 0) {
                    if (existingBadge) {
                        existingBadge.textContent = qrNew;
                    } else {
                        var sp2 = document.createElement('span');
                        sp2.className = 'sidebar-badge';
                        sp2.textContent = qrNew;
                        link.appendChild(sp2);
                    }
                } else if (existingBadge) {
                    existingBadge.remove();
                }
            }
            if (href.indexOf('/admin/reviews') !== -1) {
                if (reviewPending > 0) {
                    if (existingBadge) {
                        existingBadge.textContent = reviewPending;
                    } else {
                        var sp3 = document.createElement('span');
                        sp3.className = 'sidebar-badge';
                        sp3.textContent = reviewPending;
                        link.appendChild(sp3);
                    }
                } else if (existingBadge) {
                    existingBadge.remove();
                }
            }
        });
    }

    // ── Build a notification list item HTML ────────────────────────
    function notifIcon(type) {
        if (type === 'appointment') return '<svg class="icon"><use href="#icon-calendar"/></svg>';
        if (type === 'qr_payment') return '<svg class="icon"><use href="#icon-zap"/></svg>';
        if (type === 'review') return '<svg class="icon"><use href="#icon-star"/></svg>';
        return '<svg class="icon"><use href="#icon-shield"/></svg>';
    }

    function buildNotifItem(n) {
        var iconClass = 'notif-icon-' + (n.type || 'system');
        var link = base + (n.link || '/admin');
        var time = n.created_at || '';
        return '<li class="notif-item unread" data-id="' + (n.id || '') + '">' +
            '<a href="' + link + '" class="notif-item-link">' +
            '<span class="notif-icon ' + iconClass + '">' + notifIcon(n.type) + '</span>' +
            '<div class="notif-item-body">' +
            '<strong>' + escHtml(n.title || '') + '</strong>' +
            '<span>' + escHtml(n.message || '') + '</span>' +
            '<time>' + escHtml(time) + '</time>' +
            '</div></a></li>';
    }

    function escHtml(s) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(s));
        return d.innerHTML;
    }

    // ── Poll for updates ───────────────────────────────────────────
    function poll() {
        var url = base + '/admin/notifications/updates?since=' + lastCheck;
        fetch(url, { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok) return;

                // Update badge
                setBadge(data.unread);

                // Update sidebar badges
                updateSidebarBadges(data.appt_new, data.qr_new, data.review_pending);

                // If there are NEW notifications (not the first load)
                if (!isFirstPoll && data.new && data.new.length > 0) {
                    // Play sound
                    playNotifSound();

                    // Prepend new items to the dropdown list
                    if (list) {
                        // Remove "no notifications" placeholder
                        var empty = list.querySelector('.notif-empty');
                        if (empty) empty.remove();

                        // Add new items at the top
                        var html = '';
                        data.new.forEach(function (n) {
                            html += buildNotifItem(n);
                        });
                        list.insertAdjacentHTML('afterbegin', html);

                        // Keep only 20 items in the DOM
                        while (list.children.length > 20) {
                            list.removeChild(list.lastChild);
                        }
                    }

                    // Show a toast notification
                    data.new.forEach(function (n) {
                        showToast(n.title, n.message, base + (n.link || '/admin'));
                    });
                }

                lastCheck = data.time;
                isFirstPoll = false;
            })
            .catch(function () {}); // silently ignore network errors
    }

    // ── Toast notification ─────────────────────────────────────────
    function showToast(title, message, link) {
        var toast = document.createElement('div');
        toast.className = 'notif-toast';
        toast.innerHTML = '<strong>' + escHtml(title) + '</strong><span>' + escHtml(message) + '</span>';
        toast.addEventListener('click', function () {
            if (link) window.location.href = link;
        });
        document.body.appendChild(toast);
        // Animate in
        requestAnimationFrame(function () {
            toast.classList.add('show');
        });
        // Remove after 6 seconds
        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () { toast.remove(); }, 400);
        }, 6000);
    }

    // ── Start polling ──────────────────────────────────────────────
    poll(); // immediate first check
    setInterval(poll, pollInterval);
})();

// ─── Password eye toggle + strength indicator ────────────────────
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.pw-toggle');
    if (!btn) return;
    e.preventDefault();
    var targetId = btn.getAttribute('data-pw-target');
    var input = targetId ? document.getElementById(targetId) : btn.previousElementSibling;
    if (!input) return;
    var useEl = btn.querySelector('use');
    if (input.type === 'password') {
        input.type = 'text';
        if (useEl) useEl.setAttribute('href', '#icon-eye-off');
    } else {
        input.type = 'password';
        if (useEl) useEl.setAttribute('href', '#icon-eye');
    }
});

(function () {
    var pw = document.getElementById('password');
    var bar = document.getElementById('pwBar');
    var text = document.getElementById('pwText');
    var match = document.getElementById('pwMatch');
    var confirm = document.getElementById('confirm_password');
    if (pw) {

    pw.addEventListener('input', function () {
        var v = pw.value;
        var score = 0;
        if (v.length >= 8) score++;
        if (v.length >= 12) score++;
        if (/[a-z]/.test(v) && /[A-Z]/.test(v)) score++;
        if (/\d/.test(v)) score++;
        if (/[^a-zA-Z0-9]/.test(v)) score++;

        var colors = ['#dc2626', '#f97316', '#eab308', '#22c55e', '#16a34a'];
        var labels = ['Weak', 'Fair', 'Good', 'Strong', 'Very Strong'];
        var idx = Math.min(score, 4);

        if (bar) {
            bar.style.width = ((idx + 1) / 5 * 100) + '%';
            bar.style.background = colors[idx];
        }
        if (text) {
            text.textContent = v.length > 0 ? labels[idx] : '';
            text.style.color = colors[idx];
        }

        if (confirm && match) {
            if (confirm.value !== '' && confirm.value === v) {
                match.textContent = '✓ Passwords match';
                match.style.color = '#16a34a';
            } else if (confirm.value !== '') {
                match.textContent = '✗ Passwords don\'t match';
                match.style.color = '#dc2626';
            } else {
                match.textContent = '';
            }
        }
    });

    if (confirm) {
        confirm.addEventListener('input', function () {
            if (!match) return;
            if (confirm.value === pw.value && confirm.value !== '') {
                match.textContent = '\u2713 Passwords match';
                match.style.color = '#16a34a';
            } else if (confirm.value !== '') {
                match.textContent = '\u2717 Passwords don\'t match';
                match.style.color = '#dc2626';
            } else {
                match.textContent = '';
            }
        });
    }

    /* ---------- Star rating input ---------- */
    var ratingInputs = document.querySelectorAll('.rating-input');
    ratingInputs.forEach(function (ratingInput) {
        var stars = ratingInput.querySelectorAll('.rating-star');
        var inputs = ratingInput.querySelectorAll('input[type="radio"]');

        // The stars are displayed in reverse order due to row-reverse,
        // so index 0 in DOM = star 5, index 4 = star 1
        function getStarValue(index) {
            // With 5 stars and row-reverse: DOM index 0 = value 5, index 4 = value 1
            return 5 - index;
        }

        // Set visual state based on checked radio
        function updateVisual() {
            var checkedInput = null;
            inputs.forEach(function (input) {
                if (input.checked) checkedInput = input;
            });

            stars.forEach(function (star, i) {
                var icon = star.querySelector('.icon');
                var starValue = getStarValue(i);

                if (checkedInput && parseInt(checkedInput.value) >= starValue) {
                    icon.style.color = 'var(--apricot, #f4a261)';
                } else {
                    icon.style.color = '';
                }
            });
        }

        // Hover effects - highlight stars based on hovered star's value
        stars.forEach(function (star, index) {
            var starValue = getStarValue(index);

            star.addEventListener('mouseenter', function () {
                stars.forEach(function (s, i) {
                    var sValue = getStarValue(i);
                    if (sValue >= starValue) {
                        s.querySelector('.icon').style.color = 'var(--apricot, #f4a261)';
                    }
                });
            });

            star.addEventListener('mouseleave', updateVisual);

            // Click to select this star's value
            star.addEventListener('click', function () {
                var valueToSelect = getStarValue(index);
                inputs.forEach(function (input) {
                    if (parseInt(input.value) === valueToSelect) {
                        input.checked = true;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                updateVisual();
            });
        });

        // Listen for changes
        inputs.forEach(function (input) {
            input.addEventListener('change', updateVisual);
        });

        // Initialize
        updateVisual();
    });

    }

    /* ---------- About page founder cards: inline bio expand/collapse ---------- */
    (function () {
        var toggles = document.querySelectorAll('[data-bio-toggle]');
        if (toggles.length === 0) return;

        var DURATION = 320; // ms — must match the height transition set below

        function clearTimer(panel) {
            if (panel._bioTimer) { clearTimeout(panel._bioTimer); panel._bioTimer = null; }
        }

        /* After the height animation ends: fully open → height auto + overflow
           visible; fully closed → hidden again. Overflow is ALWAYS reset here,
           so a stalled transition can never leave text overlapping the page. */
        function finish(panel) {
            clearTimer(panel);
            if (panel.getAttribute('data-open') === '1') {
                panel.style.transition = '';
                panel.style.overflow = '';
                panel.style.height = 'auto';
            } else {
                panel.hidden = true;
                panel.style.transition = '';
                panel.style.overflow = '';
                panel.style.height = '';
            }
        }

        function setOpen(panel, open) {
            var inner = panel.querySelector('.founder-card-more-inner');
            if (!inner) return;
            clearTimer(panel);

            if (open) {
                panel.hidden = false;
                panel.style.overflow = 'hidden';
                panel.style.height = '0px';
                void panel.offsetHeight; // reflow so the transition starts from 0
                panel.style.transition = 'height ' + DURATION + 'ms ease';
                panel.style.height = inner.offsetHeight + 'px';
                panel.setAttribute('data-open', '1');
            } else {
                // Animate from the CURRENT rendered height (the panel may be at
                // height:auto after opening — animating straight from auto to 0
                // never runs, which previously left the text overlapping).
                panel.style.overflow = 'hidden';
                panel.style.height = panel.scrollHeight + 'px';
                void panel.offsetHeight; // reflow at the measured height
                panel.style.transition = 'height ' + DURATION + 'ms ease';
                panel.style.height = '0px';
                panel.removeAttribute('data-open');
            }
            // Fallback in case transitionend never fires (e.g. reduced motion)
            panel._bioTimer = setTimeout(function () { finish(panel); }, DURATION + 80);
        }

        toggles.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panel = document.querySelector(btn.getAttribute('data-bio-toggle'));
                if (!panel) return;
                var opening = btn.getAttribute('aria-expanded') !== 'true';

                // Keep every trigger of the SAME panel in sync
                toggles.forEach(function (other) {
                    if (other.getAttribute('data-bio-toggle') === btn.getAttribute('data-bio-toggle')) {
                        other.setAttribute('aria-expanded', opening ? 'true' : 'false');
                    }
                });
                // Close any other open founder panel (one open at a time)
                document.querySelectorAll('.founder-card-more').forEach(function (otherPanel) {
                    if (otherPanel !== panel && otherPanel.getAttribute('data-open') === '1') {
                        setOpen(otherPanel, false);
                    }
                });

                setOpen(panel, opening);
            });
        });

        document.querySelectorAll('.founder-card-more').forEach(function (panel) {
            panel.addEventListener('transitionend', function (e) {
                if (e.propertyName === 'height') finish(panel);
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            document.querySelectorAll('.founder-card-more').forEach(function (panel) {
                if (panel.hidden || panel.getAttribute('data-open') !== '1') return;
                document.querySelectorAll('[data-bio-toggle][aria-expanded="true"]').forEach(function (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                });
                setOpen(panel, false);
            });
        });
    })();

    /* ---------- About page info-box modals ---------- */
    (function () {
        var modals = document.querySelectorAll('.info-modal');
        if (modals.length === 0) return;

        var lastFocus = null;

        function openModal(modal) {
            lastFocus = document.activeElement;
            modal.hidden = false;
            document.body.classList.add('modal-open');
            var closeBtn = modal.querySelector('.info-modal-close');
            if (closeBtn) closeBtn.focus();
        }

        function closeModal(modal) {
            modal.hidden = true;
            document.body.classList.remove('modal-open');
            if (lastFocus && typeof lastFocus.focus === 'function') lastFocus.focus();
        }

        document.querySelectorAll('[data-modal-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var modal = document.querySelector(btn.getAttribute('data-modal-target'));
                if (modal) openModal(modal);
            });
        });

        modals.forEach(function (modal) {
            modal.querySelectorAll('[data-modal-close]').forEach(function (el) {
                el.addEventListener('click', function () { closeModal(modal); });
            });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            modals.forEach(function (modal) {
                if (!modal.hidden) closeModal(modal);
            });
        });
    })();
})();
