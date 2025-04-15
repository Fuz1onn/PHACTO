function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    const togglePasswordIcon = document.querySelector(".toggle-password");
    
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePasswordIcon.classList.remove("bx-hide");
      togglePasswordIcon.classList.add("bx-show");
    } else {
      passwordInput.type = "password";
      togglePasswordIcon.classList.remove("bx-show");
      togglePasswordIcon.classList.add("bx-hide");
    }
  }

  // Function to toggle password visibility for "Confirm Password" input
  function toggleConfirmPasswordVisibility() {
    const confirmPasswordInput = document.getElementById("cpassword");
    const toggleConfirmPasswordIcon = document.querySelector("#toggleConfirmPasswordBtn");
    
    if (confirmPasswordInput.type === "password") {
      confirmPasswordInput.type = "text";
      toggleConfirmPasswordIcon.classList.remove("bx-hide");
      toggleConfirmPasswordIcon.classList.add("bx-show");
    } else {
      confirmPasswordInput.type = "password";
      toggleConfirmPasswordIcon.classList.remove("bx-show");
      toggleConfirmPasswordIcon.classList.add("bx-hide");
    }
  }

  // Event listeners for the toggle password buttons
  document.querySelector("#togglePasswordBtn").addEventListener("click", togglePasswordVisibility);
  document.querySelector("#toggleConfirmPasswordBtn").addEventListener("click", toggleConfirmPasswordVisibility);