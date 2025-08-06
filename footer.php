<!-- footer.php -->
<footer class="bg-white text-center text-muted mt-5 py-4 border-top animate__animated animate__fadeInUp">
  <div class="container">
    <small>&copy; <?= date('Y') ?> <strong>eTest</strong>. All rights reserved. Designed by <strong>UZHOST Inc.</strong>.</small>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute('href')).scrollIntoView({
        behavior: 'smooth'
      });
    });
  });
</script>
</body>
</html>
