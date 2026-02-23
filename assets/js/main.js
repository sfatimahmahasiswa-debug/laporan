// Konfirmasi hapus
function konfirmasiHapus(url, nama) {
  if (confirm('Apakah Anda yakin ingin menghapus "' + nama + '"?')) {
    window.location.href = url;
  }
}

// Auto-hide alert setelah 4 detik
document.addEventListener('DOMContentLoaded', function () {
  var alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(function (alert) {
    setTimeout(function () {
      var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      bsAlert.close();
    }, 4000);
  });
});
