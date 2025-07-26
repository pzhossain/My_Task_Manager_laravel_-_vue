import { defineStore } from "pinia";
import { computed, ref } from "vue";
import apiClient from "../services/axiosClient";
import { useRouter } from "vue-router";
import cogoToast from "cogo-toast";

export const useAuthStore = defineStore("auth", () => {
  const router = useRouter();

  // State
  const user = ref(null);
 
  // Fetch user from localStorage if available)
  const token = ref(localStorage.getItem("token") || null);
  const isAuthenticated = computed(() => token.value !== null);

  //Actions

  //Registration
  const register = async (credentials) => {
    try {
      await apiClient.post("/register", credentials);
      cogoToast.success("Registration Successfull", {
        position: "bottom-center",
      });
      return true;
    } catch (error) {
      //   validation error
      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        for (const field in errors) {
          errors[field].forEach((msg) => {
            cogoToast.error(msg, { position: "bottom-center" });
          });
        }
      }
      //   Email already Taken
      else if (error.response?.data?.message) {
        cogoToast.error(error.response.data.message, {
          position: "bottom-center",
        });
      }
      // Server or Network
      else {
        cogoToast.error("Something went wrong", {
          position: "bottom-center",
        });
      }
      return false;
    }
  };

  //Login
  const login = async (credentials) => {
    try {
      const res = await apiClient.post("/login", credentials);      
      user.value = res.data.user;
      // console.log("login user:", user.value);
      token.value = res.data.token;
      localStorage.setItem("token", token.value);
      cogoToast.success("Login Successfull", { position: "bottom-center" });
      return true;
    } catch (error) {
      console.log(error);
      //   validation error
      if (error.response?.data?.errors) {
        const errors = error.response.data.errors;
        for (const field in errors) {
          errors[field].forEach((msg) => {
            cogoToast.error(msg, { position: "bottom-center" });
          });
        }
      }
      //   Invalid Credentials
      else if (error.response?.data?.message) {
        cogoToast.error(error.response.data.message, {
          position: "bottom-center",
        });
      }
      // Server or Network
      else {
        cogoToast.error("Something went wrong", {
          position: "bottom-center",
        });
      }
    }
  };

  // Logout
  const logout = async () => {
    try {
      const success = await apiClient.post("/logout");
      token.value = null;
      localStorage.removeItem("token");
      cogoToast.success("Logout Successfull", { position: "bottom-center" });

      if (success) {
        router.push({ name: "login" });
      }

      return true;
    } catch (error) {
      console.log(error);
      if (error?.message) {
        cogoToast.error(error.message, {
          position: "bottom-center",
        });
      }
      return false;
    }
  };

  const getUser = async () => {
    try {
      const res = await apiClient.get("/me");      
      user.value = res.data.data;
      // console.log("UserDetails:", user.value);
    } catch (error) {
      console.log("Failed to fetch user", error);
    }
  };

  // Update user details
  const updateUser = async (userData) => {
    try{
      const res = await apiClient.put("/update", userData);
      user.value = res.data.data;
      cogoToast.success("User updated successfully", {
        position: "bottom-center",
      });
      return true;

    }catch (error) {
      console.log("Failed to update user", error);
      if (error?.response?.data?.message) {
        cogoToast.error(error.response.data.message, {
          position: "bottom-center",
        });
      } else {
        cogoToast.error("Something went wrong", {
          position: "bottom-center",
        });
      }
    }
  }

  // Get User
  return {
    user,
    token,
    isAuthenticated,
    register,
    login,
    logout,
    getUser,
  };
});
