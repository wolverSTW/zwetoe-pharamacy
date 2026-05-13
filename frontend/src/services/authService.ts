import axiosInstance from "@/lib/axios";
import { AxiosError } from "axios";
import { User } from "@/types/store";

interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterData {
  name: string;
  email: string;
  phone?: string;
  password: string;
  password_confirmation: string;
}

interface AuthResponse {
  status: string;
  message: string;
  token: string;
  user?: Partial<User>;
  customer?: Partial<User>;
  data?: Partial<User>;
}

interface UserResponse {
  status: string;
  user?: Partial<User>;
  data?: Partial<User>;
}

export const authService = {
  // 1. Register a new customer
  register: async (userData: RegisterData): Promise<AuthResponse> => {
    try {
      const { data } = await axiosInstance.post<AuthResponse>("/register", userData);
      return data;
    } catch (error) {
      const axiosErr = error as AxiosError<{ message?: string }>;
      throw axiosErr.response?.data || { message: "Registration failed" };
    }
  },

  // 2. Login and save token to local storage
  login: async (credentials: LoginCredentials): Promise<AuthResponse> => {
    try {
      const { data } = await axiosInstance.post<AuthResponse>("/login", credentials);
      if (data.token) {
        localStorage.setItem("token", data.token);
      }
      return data;
    } catch (error) {
      const axiosErr = error as AxiosError<{ message?: string }>;
      throw axiosErr.response?.data || { message: "Login failed" };
    }
  },

  // 3. FETCH CURRENT LOGGED-IN USER PROFILE
  getCurrentUser: async (): Promise<UserResponse> => {
    try {
      const { data } = await axiosInstance.get<UserResponse>("/user");
      return data; 
    } catch (error) {
      const axiosErr = error as AxiosError<{ message?: string }>;
      throw axiosErr.response?.data || { message: "Could not fetch user profile" };
    }
  },

  // 4. Logout and clear local storage
  logout: async (): Promise<void> => {
    try {
      await axiosInstance.post("/logout");
    } finally {
      localStorage.removeItem("token");
      window.location.href = "/login";
    }
  }
};
