"use client";
import React, { createContext, useContext, useEffect, useState, useCallback } from "react";
import { authService } from "@/services/authService";
import { User } from "@/types/store";

interface LoginCredentials {
  email: string;
  password: string;
}

interface AuthContextType {
  user: User | null;
  token: string | null;
  loading: boolean;
  login: (credentials: LoginCredentials) => Promise<void>;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const normalizeUser = (candidate: Partial<User> | null | undefined): User | null => {
  if (!candidate || typeof candidate.id !== "number" || typeof candidate.email !== "string") {
    return null;
  }

  const displayName =
    typeof candidate.name === "string" && candidate.name.trim()
      ? candidate.name
      : typeof candidate.customer_name === "string" && candidate.customer_name.trim()
        ? candidate.customer_name
        : typeof candidate.username === "string" && candidate.username.trim()
          ? candidate.username
          : candidate.email.split("@")[0];

  return {
    ...candidate,
    id: candidate.id,
    email: candidate.email,
    name: displayName,
  } as User;
};

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  const logout = useCallback(() => {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    setUser(null);
    setToken(null);
    window.location.href = "/";
  }, []);

  useEffect(() => {
    const initAuth = async () => {
      const savedToken = localStorage.getItem("token");
      const savedUser = localStorage.getItem("user");

      if (savedToken && savedUser && savedUser !== "undefined") {
        const parsedUser = normalizeUser(JSON.parse(savedUser) as Partial<User>);
        if (parsedUser) {
          setToken(savedToken);
          setUser(parsedUser);
        }
      }

      // Release the UI immediately; token validation can finish in the background.
      setLoading(false);

      if (!savedToken) {
        return;
      }

      try {
        const freshUserData = await authService.getCurrentUser();
        const actualUserData = normalizeUser(freshUserData.user || freshUserData.data);

        if (actualUserData) {
          setUser((prevUser) => {
            const updatedUser = { ...prevUser, ...actualUserData } as User;
            localStorage.setItem("user", JSON.stringify(updatedUser));
            return updatedUser;
          });
        }
      } catch (error: unknown) {
        const err = error as { status?: number; response?: { status?: number }; message?: string };
        console.warn("Auth sync warning:", err.message || "Network issue");
        // Only force logout if the server specifically rejects the token
        if (err.status === 401 || err.response?.status === 401) {
          console.error("Session expired - logging out");
          logout();
        }
      }
    };

    initAuth();
  }, [logout]);

  const login = async (credentials: LoginCredentials) => {
    const data = await authService.login(credentials);
    const userData = normalizeUser(data.customer || data.user || data.data);
    if (!userData) {
      throw new Error("Login response did not include a valid user");
    }

    setUser(userData);
    setToken(data.token);
    localStorage.setItem("token", data.token);
    localStorage.setItem("user", JSON.stringify(userData));
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
