<!-- Footer with proper RTL/LTR support that spans the entire width -->
<footer class="bg-dark text-white py-4 mt-5 w-100">
    <div class="container-fluid">
        <div class="container">
            <div class="row">
                <!-- Platform info column -->
                <?php if (isset($_SESSION['language']) && $_SESSION['language'] == 'en'): ?>
                    <!-- English (LTR) Footer -->
                    <div class="col-md-4 mb-3 mb-md-0 text-start">
                        <h5 class="mb-3">Arabic Knowledge Platform</h5>
                        <p>A platform for exchanging knowledge between software developers, data scientists and AI specialists in Arabic</p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0 text-center">
                        <h5 class="mb-3">Important Links</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Privacy Policy</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">Terms of Use</a></li>
                            <li><a href="#" class="text-white text-decoration-none">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-end">
                        <h5 class="mb-3">Follow Us</h5>
                        <div class="social-icons">
                            <a href="#" class="text-white me-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in fa-lg"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-github fa-lg"></i></a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Arabic (RTL) Footer -->
                    <div class="col-md-4 mb-3 mb-md-0 text-end">
                        <h5 class="mb-3">منصة المعرفة العربية</h5>
                        <p>منصة لتبادل المعرفة بين مطوري البرمجيات وعلماء البيانات والذكاء الاصطناعي باللغة العربية</p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0 text-center">
                        <h5 class="mb-3">روابط مهمة</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">سياسة الخصوصية</a></li>
                            <li class="mb-2"><a href="#" class="text-white text-decoration-none">شروط الاستخدام</a></li>
                            <li><a href="#" class="text-white text-decoration-none">تواصل معنا</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 text-start">
                        <h5 class="mb-3">تابعنا على</h5>
                        <div class="social-icons">
                            <a href="#" class="text-white ms-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                            <a href="#" class="text-white ms-3"><i class="fab fa-twitter fa-lg"></i></a>
                            <a href="#" class="text-white ms-3"><i class="fab fa-linkedin-in fa-lg"></i></a>
                            <a href="#" class="text-white"><i class="fab fa-github fa-lg"></i></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Copyright row -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <?php if (isset($_SESSION['language']) && $_SESSION['language'] == 'en'): ?>
                        <p class="mb-0">© 2025 Arabic Knowledge Platform. All rights reserved.</p>
                    <?php else: ?>
                        <p class="mb-0">© 2025 منصة المعرفة العربية. جميع الحقوق محفوظة.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="assets/js/main.js"></script>
</body>
</html>