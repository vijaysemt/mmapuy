
document.addEventListener('DOMContentLoaded', function () {

    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');

    if (toggle && sidebar) {

        toggle.addEventListener('click', function () {

            sidebar.classList.toggle('show');

        });

        document.addEventListener('click', function (event) {

            if (
                window.innerWidth <= 991 &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target)
            ) {
                sidebar.classList.remove('show');
            }

        });

    }

});
