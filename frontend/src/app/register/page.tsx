"use client";

import React, { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";

export default function RegisterPage() {
  const router = useRouter();
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phone: "",
    address: "",
    password: "",
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    
    // Logic: API call for user registration
    console.log("Registering user:", formData);
    
    await new Promise((resolve) => setTimeout(resolve, 2000));
    setIsLoading(false);
    router.push("/login"); // Successful registration redirect
  };

  return (
    <div className="min-h-screen bg-[#0a0c10] flex items-center justify-center p-6 text-white antialiased">
      <div className="relative w-full max-w-lg">
        
        {/* Decorative background glow */}
        <div className="absolute -top-20 -right-20 w-64 h-64 bg-emerald-500/5 blur-[100px] rounded-full"></div>

        <div className="relative bg-[#161b22] border border-white/10 p-10 rounded-[2.5rem] shadow-2xl">
          
          {/* Brand Identity */}
          <div className="flex flex-col items-center mb-8">
            <Link href="/" className="inline-flex items-center gap-2 mb-2">
              <div className="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center text-[#0d1117] shadow-lg shadow-emerald-500/20">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </div>
            </Link>
            <h1 className="text-2xl font-black tracking-tight">
              ZweToe <span className="text-emerald-500">Pharmacy</span>
            </h1>
            <p className="text-lg pt-3 font-bold">Create Account</p>
          </div>

          <form onSubmit={handleSubmit} className="space-y-5">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              {/* Full Name */}
              <div>
                <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Full Name</label>
                <input 
                  name="fullName" type="text" required
                  value={formData.fullName} onChange={handleChange}
                  className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-800"
                  placeholder="Your Name"
                />
              </div>

              {/* Phone Number */}
              <div>
                <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Phone Number</label>
                <input 
                  name="phone" type="tel" required
                  value={formData.phone} onChange={handleChange}
                  className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-800"
                  placeholder="09XXXXXXXXX"
                />
              </div>
            </div>

            {/* Email Address */}
            <div>
              <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Email Address</label>
              <input 
                name="email" type="email" required
                value={formData.email} onChange={handleChange}
                className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-800"
                placeholder="example@mail.com"
              />
            </div>

            {/* Address */}
            <div>
              <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Full Address</label>
              <textarea 
                name="address" rows={2} required
                value={formData.address} onChange={handleChange}
                className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-800 resize-none"
                placeholder="Street, Township, City"
              />
            </div>

            {/* Password Field */}
            <div>
              <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Password</label>
              <div className="relative">
                <input 
                  name="password" type={showPassword ? "text" : "password"} required
                  value={formData.password} onChange={handleChange}
                  className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-5 py-3.5 text-white focus:outline-none focus:border-emerald-500/50 transition-all pr-12 placeholder:text-gray-800"
                  placeholder="••••••••"
                />
                <button
                  type="button" onClick={() => setShowPassword(!showPassword)}
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

            {/* Premium Submit Button */}
            <button 
              type="submit" disabled={isLoading}
              className="w-full py-4 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-900/50 text-white font-bold tracking-wide rounded-2xl shadow-xl transition-all active:scale-[0.98] mt-6"
            >
              {isLoading ? "Creating Account..." : "Register Now"}
            </button>
          </form>

          <p className="text-center mt-8 text-sm text-gray-500 font-medium">
            Already have an account? {" "}
            <Link href="/login" className="text-emerald-500 font-bold hover:underline">Sign In</Link>
          </p>
        </div>
      </div>
    </div>
  );
}