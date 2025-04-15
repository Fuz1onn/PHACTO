
  const passwordInput = document.getElementById("password");
  const togglePassword = document.querySelector(".toggle-password");

  togglePassword.addEventListener("click", () => {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePassword.classList.remove("bx-hide");
      togglePassword.classList.add("bx-show");
    } else {
      passwordInput.type = "password";
      togglePassword.classList.remove("bx-show");
      togglePassword.classList.add("bx-hide");
    }
  });
