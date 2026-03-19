"use client";

import React, { useState } from "react";
import Link from "next/link";

export default function ForgotPasswordPage() {
  const [formData, setFormData] = useState({
    fullName: "",
    phoneNumber: "",
  });

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isRequestSent, setIsRequestSent] = useState(false);

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSubmitting(true);

    try {
      console.log("Submitting reset request for:", formData);
      await new Promise((resolve) => setTimeout(resolve, 1500));
      setIsRequestSent(true);
    } catch (error) {
      console.error("Failed to send request:", error);
    } finally {
      setIsSubmitting(false);
    }
  };

  return (
    <div className="min-h-screen bg-[#0a0c10] flex items-center justify-center p-6 text-white antialiased">
      <div className="relative w-full max-w-md">
        
        {/* Card Container */}
        <div className="relative bg-[#161b22] border border-white/10 p-10 rounded-[2.5rem] shadow-2xl">
          
          {/* Brand Identity - Clean & Straight */}
          <div className="flex flex-col items-center mb-10">
            <Link href="/" className="inline-flex items-center gap-2 mb-4">
              <div className="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-[#0d1117] shadow-lg shadow-emerald-500/20">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              </div>
            </Link>
            <h1 className="text-2xl font-black tracking-tight text-white">
              ZweToe<span className="text-emerald-500"> Pharmacy</span>
            </h1>
          </div>

          {!isRequestSent ? (
            <>
              {/* Header Section - Balanced Spacing */}
              <div className="text-center mb-10">
                <h2 className="text-lg font-bold tracking-tight text-white mb-2">Account Recovery</h2>
                <p className="text-gray-400 text-sm font-medium opacity-80 leading-relaxed">
                  Provide your details for manual verification by our staff.
                </p>
              </div>
              
              <form onSubmit={handleSubmit} className="space-y-6">
                {/* Full Name Input */}
                <div>
                  <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2.5 ml-1">Full Name</label>
                  <input 
                    name="fullName"
                    type="text" 
                    required
                    value={formData.fullName}
                    onChange={handleInputChange}
                    className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-6 py-4 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-700 font-medium"
                    placeholder="Enter your name"
                  />
                </div>

                {/* Phone Number Input */}
                <div>
                  <label className="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2.5 ml-1">Phone Number</label>
                  <input 
                    name="phoneNumber"
                    type="tel" 
                    required
                    value={formData.phoneNumber}
                    onChange={handleInputChange}
                    className="w-full bg-[#0d1117] border border-white/5 rounded-2xl px-6 py-4 text-white focus:outline-none focus:border-emerald-500/50 transition-all placeholder:text-gray-700 font-medium"
                    placeholder="09XXXXXXXXX"
                  />
                </div>

                {/* Submit Button - Refined Typography */}
                <button 
                  type="submit" 
                  disabled={isSubmitting}
                  className="w-full py-4 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-900/50 text-white font-bold  tracking-wide rounded-2xl shadow-xl transition-all active:scale-[0.98] mt-4"
                >
                  {isSubmitting ? "Submitting..." : "Send Request"}
                </button>
              </form>
            </>
          ) : (
            /* Success View */
            <div className="text-center py-6 animate-in fade-in zoom-in duration-500">
              <div className="w-20 h-20 bg-emerald-500/10 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-500/20">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </div>
              <h2 className="text-xl font-bold tracking-tight mb-2">Request Sent!</h2>
              <p className="text-gray-400 text-sm font-medium mb-10 leading-relaxed">
                Staff will contact you via phone shortly to help you reset your password.
              </p>
              <Link 
                href="/login" 
                className="text-emerald-500 font-bold hover:underline transition-all text-sm"
              >
                Back to Login
              </Link>
            </div>
          )}

          {/* Bottom Footer Link */}
          {!isRequestSent && (
            <p className="text-center mt-10 text-sm text-gray-500 font-medium">
              Remember password? {" "}
              <Link href="/login" className="text-emerald-500 font-bold hover:underline transition-colors ml-1">Sign In</Link>
            </p>
          )}
        </div>
      </div>
    </div>
  );
}