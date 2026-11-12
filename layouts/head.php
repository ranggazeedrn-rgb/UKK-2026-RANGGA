<!-- layouts/head.php -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
    input:not([type="checkbox"]):not([type="radio"]):focus,
    select:focus, textarea:focus { box-shadow: 0 0 0 3px rgba(37, 99, 235, .15); }
    input:user-invalid, select:user-invalid, textarea:user-invalid { border-color: #f87171; }
    textarea { resize: vertical; }
    button:disabled, input[type="submit"]:disabled { opacity: .6; cursor: not-allowed; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hubungkan label ke kolom terdekat supaya klik label memfokuskan isian.
        document.querySelectorAll('label').forEach(function (label) {
            if (label.htmlFor) return;
            var field = label.nextElementSibling;
            if (!field || !/^(INPUT|SELECT|TEXTAREA)$/.test(field.tagName)) {
                field = label.parentElement.querySelector('input, select, textarea');
            }
            if (!field) return;
            if (!field.id) field.id = 'auto' + Math.random().toString(36).slice(2, 8);
            label.htmlFor = field.id;
        });

        // Cegah submit ganda; ditunda agar nilai tombol submit tetap ikut terkirim.
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form.checkValidity || !form.checkValidity()) return;
            setTimeout(function () {
                if (e.defaultPrevented) return;
                form.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (b) { b.disabled = true; });
            }, 0);
        });
    });
</script>
