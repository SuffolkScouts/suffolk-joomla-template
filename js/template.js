(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
      return;
    }
    document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    var header = document.querySelector('[data-sf-header]');
    var nav = document.querySelector('[data-sf-nav]');
    var navToggle = document.querySelector('[data-sf-nav-toggle]');
    var backdrop = document.querySelector('[data-sf-menu-backdrop]');
    var closeTimer = null;

    function closeMenus() {
      document.querySelectorAll('.sf-nav__item.is-open').forEach(function (item) {
        item.classList.remove('is-open');
        var trigger = item.querySelector('[data-sf-menu-trigger]');
        if (trigger) {
          trigger.setAttribute('aria-expanded', 'false');
        }
      });

      if (header) {
        header.classList.remove('has-open-menu');
      }
    }

    function openMenu(item) {
      if (closeTimer) {
        clearTimeout(closeTimer);
        closeTimer = null;
      }

      closeMenus();
      item.classList.add('is-open');

      var trigger = item.querySelector('[data-sf-menu-trigger]');
      if (trigger) {
        trigger.setAttribute('aria-expanded', 'true');
      }

      if (header && window.matchMedia('(min-width: 981px)').matches) {
        header.classList.add('has-open-menu');
      }
    }

    document.querySelectorAll('.sf-nav__item.has-menu').forEach(function (item) {
      var trigger = item.querySelector('[data-sf-menu-trigger]');
      var menu = item.querySelector('[data-sf-menu]');

      item.addEventListener('mouseenter', function () {
        if (window.matchMedia('(min-width: 981px)').matches) {
          openMenu(item);
        }
      });

      item.addEventListener('mouseleave', function () {
        if (window.matchMedia('(min-width: 981px)').matches) {
          closeTimer = setTimeout(closeMenus, 120);
        }
      });

      if (menu) {
        menu.addEventListener('mouseenter', function () {
          if (closeTimer) {
            clearTimeout(closeTimer);
            closeTimer = null;
          }
        });
      }

      if (trigger) {
        trigger.addEventListener('click', function (event) {
          event.preventDefault();
          if (item.classList.contains('is-open')) {
            closeMenus();
          } else {
            openMenu(item);
          }
        });
      }
    });

    if (navToggle && header && nav) {
      navToggle.addEventListener('click', function () {
        var isOpen = header.classList.toggle('is-mobile-open');
        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (!isOpen) {
          closeMenus();
        }
      });
    }

    if (backdrop) {
      backdrop.addEventListener('click', closeMenus);
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeMenus();
        if (header && header.classList.contains('is-mobile-open')) {
          header.classList.remove('is-mobile-open');
          if (navToggle) {
            navToggle.setAttribute('aria-expanded', 'false');
          }
        }
      }
    });

    window.addEventListener('resize', function () {
      closeMenus();
      if (window.matchMedia('(min-width: 981px)').matches && header && navToggle) {
        header.classList.remove('is-mobile-open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });

    // ---- Admin edit dropdowns (article / news-card cog buttons) ----
    function closeAllDropdowns() {
      document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
        menu.classList.remove('show');
        var toggle = menu.closest('.btn-group') && menu.closest('.btn-group').querySelector('.dropdown-toggle');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
    }

    document.addEventListener('click', function (e) {
      var toggle = e.target.closest('.dropdown-toggle[data-bs-toggle="dropdown"]');
      if (toggle) {
        e.preventDefault();
        var menu = toggle.closest('.btn-group') && toggle.closest('.btn-group').querySelector('.dropdown-menu');
        if (!menu) return;
        var isOpen = menu.classList.contains('show');
        closeAllDropdowns();
        if (!isOpen) {
          menu.classList.add('show');
          toggle.setAttribute('aria-expanded', 'true');
        }
      } else if (!e.target.closest('.dropdown-menu')) {
        closeAllDropdowns();
      }
    });

    document.querySelectorAll('.control-group').forEach(function (group) {
      group.classList.add('form-group');
    });

    document.querySelectorAll(
      '.control-group input[type=text], .control-group input[type=email], .control-group input[type=password], .control-group input[type=search], .control-group input[type=url], .control-group input[type=tel], .control-group input[type=number], .control-group input[type=date], .control-group select, .control-group textarea'
    ).forEach(function (field) {
      field.classList.add('form-control');
    });

    document.querySelectorAll('table').forEach(function (table) {
      table.classList.add('table');
    });
  });
})();
