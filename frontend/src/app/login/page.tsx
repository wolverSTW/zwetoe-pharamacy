"use client";

import React, { useState } from "react";
import Link from "next/link";
import axios from "axios";

export default function LoginPage() {
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  });

  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setError(null);

    const apiUrl = process.env.NEXT_PUBLIC_API_URL;

    try {
      const response = await axios.post(`${apiUrl}/login`, {
        email: formData.email,
        password: formData.password,
      });

      const { token, user } = response.data;

      localStorage.setItem("token", token);
      localStorage.setItem("user", JSON.stringify(user));

      window.location.href = "/";

    } catch (err: any) {
      const msg = err.response?.data?.message || "Authentication failed. Please check your credentials.";
      setError(msg);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#0a0c10] flex items-center justify-center p-6">
      <div className="relative w-full max-w-md">
        {/* Glow Background Decoration */}
        <div className="absolute -top-[10%] -left-[10%] w-40 h-40 bg-emerald-500/10 blur-[80px] rounded-full"></div>
        <div className="absolute -bottom-[10%] -right-[10%] w-40 h-40 bg-emerald-500/10 blur-[80px] rounded-full"></div>

        <div className="relative bg-[#161b22] border border-white/5 p-8 rounded-3xl shadow-2xl backdrop-blur-sm">
          
          <div className="text-center mb-10">
            <Link href="/" className="inline-flex items-center gap-2 mb-4 group">
              <div className="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-[#0d1117] transition-transform group-hover:scale-110">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </div>
              <span className="text-2xl font-black text-white tracking-wide">ZweToe<span className="text-emerald-500"> Pharmacy</span></span>
            </Link>
            <p className="text-gray-500 text-[10px] font-black uppercase tracking-[0.2em]">Authorized Access Only</p>
          </div>

          {error && (
            <div className="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-500 text-[10px] font-black uppercase text-center tracking-wider animate-shake">
              {error}
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-5">
            <div>
              <label className="block text-[10px] font-black text-gray-500 uppercase tracking-wide mb-2 ml-1">Account Email</label>
              <input 
                name="email"
                type="email" 
                required
                value={formData.email}
                onChange={handleChange}
                className="w-full bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-emerald-500/40 transition-all placeholder:text-gray-800 text-sm font-medium"
                placeholder="name@email.com"
              />
            </div>

            <div>
              <div className="flex justify-between items-center mb-2 ml-1">
                <label className="text-[10px] font-black text-gray-500 uppercase tracking-wide">Secret Password</label>
                <Link href="/forgot-password" className="text-emerald-500 text-[10px] font-black  hover:text-emerald-400 transition-colors">Forgot Password?</Link>
              </div>
              
              <div className="relative">
                <input 
                  name="password"
                  type={showPassword ? "text" : "password"} 
                  required
                  value={formData.password}
                  onChange={handleChange}
                  className="w-full bg-[#0d1117] border border-white/5 rounded-xl px-5 py-4 text-white focus:outline-none focus:border-emerald-500/40 transition-all pr-12 placeholder:text-gray-800 text-sm tracking-widest"
                  placeholder="••••••••"
                />
                
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-600 hover:text-emerald-500 transition-colors"
                >
                  {showPassword ? (
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                  ) : (
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  )}
                </button>
              </div>
            </div>

            <button 
              type="submit"
              disabled={isLoading}
              className="group relative w-full py-4 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-900/50 disabled:text-emerald-800 disabled:cursor-not-allowed text-white font-black tracking-wide text-[12px] rounded-xl shadow-xl shadow-emerald-500/10 transition-all active:scale-[0.98] mt-4 overflow-hidden"
            >
              <span className="relative z-10">{isLoading ? "Verifying..." : "Sign In"}</span>
              {isLoading && (
                <div className="absolute inset-0 bg-emerald-500/20 animate-pulse"></div>
              )}
            </button>
          </form>

          <p className="text-center mt-8 text-[11px] text-gray-600 font-bold uppercase tracking-widest">
            Don't have an account? {" "}
            <Link href="/register" className="text-emerald-500 font-black hover:text-emerald-400 underline underline-offset-4 transition-colors">Register Now</Link>
          </p>
        </div>
      </div>
    </div>
  );
}