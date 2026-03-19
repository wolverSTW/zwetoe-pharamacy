import axiosInstance from "@/lib/axios";

export const authService = {
  // 1. Register a new customer
  register: async (userData: any) => {
    try {
      const { data } = await axiosInstance.post("/register", userData);
      return data;
    } catch (error: any) {
      throw error.response?.data || { message: "Registration failed" };
    }
  },

  // 2. Login and save token to local storage
  login: async (credentials: any) => {
    try {
      const { data } = await axiosInstance.post("/login", credentials);
      if (data.token) {
        localStorage.setItem("token", data.token);
      }
      return data;
    } catch (error: any) {
      throw error.response?.data || { message: "Login failed" };
    }
  },

  // 3. FETCH CURRENT LOGGED-IN USER PROFILE (This was missing!)
  getCurrentUser: async () => {
    try {
      const { data } = await axiosInstance.get("/user");
      // Your Laravel backend returns current user data here
      return data; 
    } catch (error: any) {
      throw error.response?.data || { message: "Could not fetch user profile" };
    }
  },

  // 4. Logout and clear local storage
  logout: async () => {
    try {
      await axiosInstance.post("/logout");
    } finally {
      localStorage.removeItem("token");
      window.location.href = "/login";
    }
  }
};