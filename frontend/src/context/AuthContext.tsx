"use client";
import React, { createContext, useContext, useEffect, useState } from "react";
import { authService } from "@/services/authService";

interface AuthContextType {
  user: any;
  token: string | null;
  loading: boolean;
  login: (credentials: any) => Promise<void>;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState<any>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);


  useEffect(() => {
    const initAuth = async () => {
      const savedToken = localStorage.getItem("token");
      const savedUser = localStorage.getItem("user");

      if (savedToken && savedUser && savedUser !== "undefined") {
        const parsedUser = JSON.parse(savedUser);
        setToken(savedToken);
        setUser(parsedUser);
      }

      if (savedToken) {
        try {
          const freshUserData = await authService.getCurrentUser();
          
          if (freshUserData) {
            const actualUserData = freshUserData.user || freshUserData;
            setUser((prevUser: any) => {
              const updatedUser = { ...prevUser, ...actualUserData };
              localStorage.setItem("user", JSON.stringify(updatedUser));
              return updatedUser;
            });
          }
        } catch (error: any) {
          console.warn("Auth sync warning:", error.message || "Network issue");
          // Only force logout if the server specifically rejects the token
          if (error.status === 401 || error.response?.status === 401) {
            console.error("Session expired - logging out");
            logout();
          }
        }
      }
      setLoading(false);
    };

    initAuth();
  }, []);

  const login = async (credentials: any) => {
    const data = await authService.login(credentials);
    const userData = data.customer || data.user || data;
    setUser(userData);
    setToken(data.token);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(userData));
  };

  const logout = () => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setUser(null);
    setToken(null);
    window.location.href = "/";
  };

  return (
    <AuthContext.Provider value={{ user, token, loading, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) throw new Error("useAuth must be used within AuthProvider");
  return context;
};