/ Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Handle copy link functionality
    var copyLinkButtons = document.querySelectorAll('.copy-link');
    if (copyLinkButtons) {
        copyLinkButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                var link = this.getAttribute('data-link') || window.location.href;
                
                // Create a temporary input element
                var tempInput = document.createElement('input');
                tempInput.value = link;
                document.body.appendChild(tempInput);
                
                // Select and copy the link
                tempInput.select();
                document.execCommand('copy');
                
                // Remove the temporary input
                document.body.removeChild(tempInput);
                
                // Show a notification
                alert('تم نسخ الرابط!');
            });
        });
    }

    // Handle tag input
    var tagInput = document.getElementById('tags');
    if (tagInput) {
        // Simple tag input functionality
        tagInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                var value = this.value.trim();
                if (value) {
                    // Here you could add more sophisticated tag handling
                    // This is just a simple example
                    this.value = value + ', ';
                }
            }
        });
    }

    // Handle save question functionality
    window.saveQuestion = function (questionId) {
        // This would typically be an AJAX request
        console.log('Saving question ID:', questionId);
        // Show a notification to user
        alert('تم حفظ السؤال!');
    };

    // Handle voting functionality
    window.voteQuestion = function (questionId, voteType) {
        // This would typically be an AJAX request
        console.log('Voting on question ID:', questionId, 'with vote type:', voteType);
        // Update the UI accordingly
    };

    window.voteAnswer = function (answerId, voteType) {
        // This would typically be an AJAX request
        console.log('Voting on answer ID:', answerId, 'with vote type:', voteType);
        // Update the UI accordingly
    };

    // Handle accepting answers
    window.acceptAnswer = function (answerId) {
        // This would typically be an AJAX request
        console.log('Accepting answer ID:', answerId);
        // Update the UI accordingly
        alert('تم قبول الإجابة!');
    };

    // Handle reporting content
    window.reportQuestion = function (questionId) {
        // This would typically show a modal with report options
        console.log('Reporting question ID:', questionId);
        // For now, just show an alert
        alert('تم إرسال التقرير. شكراً لمساعدتك في الحفاظ على جودة المحتوى!');
    };

    window.reportAnswer = function (answerId) {
        // This would typically show a modal with report options
        console.log('Reporting answer ID:', answerId);
        // For now, just show an alert
        alert('تم إرسال التقرير. شكراً لمساعدتك في الحفاظ على جودة المحتوى!');
    };
});
