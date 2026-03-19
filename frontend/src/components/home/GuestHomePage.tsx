"use client";

import Link from "next/link";

export default function GuestHomePage() {
  return (
    <main className="min-h-screen bg-[#0d1117] text-white font-sans overflow-hidden">
      {/* Background Glow Effects */}
      <div className="fixed top-0 left-1/4 w-125 h-125 bg-emerald-500/5 blur-[120px] rounded-full"></div>
      <div className="fixed bottom-0 right-1/4 w-100 h-100 bg-blue-500/5 blur-[100px] rounded-full"></div>

      <div className="relative z-10 container mx-auto px-6 min-h-[calc(100vh-64px)] flex flex-col">
        {/* Simple Top Nav ကို page.tsx က Navbar နဲ့ အစားထိုးလိုက်လို့ ဒီမှာ ဖြုတ်လိုက်ပါပြီ */}

        {/* Hero Content */}
        <div className="flex-1 flex flex-col items-center justify-center text-center max-w-4xl mx-auto">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-8 mt-12">
            <span className="relative flex h-2 w-2">
              <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span className="text-emerald-500 text-[10px] font-black uppercase tracking-[0.2em]">Open 24/7 For You</span>
          </div>

          <h1 className="text-5xl md:text-7xl font-bold tracking-tight leading-[1.1] mb-8">
            Your Health, <br />
            <span className="text-transparent bg-clip-text bg-linear-to-r from-emerald-400 to-cyan-400">Our Digital Priority.</span>
          </h1>

          <p className="text-gray-500 text-lg md:text-xl max-w-2xl mb-12 leading-relaxed font-medium">
            Join thousands of satisfied customers. Create an account to browse our premium medical catalog, 
            track your orders, and get exclusive member-only discounts.
          </p>

          <div className="flex flex-col sm:flex-row gap-5 w-full sm:w-auto">
            <Link 
              href="/register" 
              className="px-12 py-5 bg-emerald-600 hover:bg-emerald-500 text-[#0d1117] rounded-2xl font-black transition-all transform hover:scale-105 shadow-2xl shadow-emerald-500/20 text-sm"
            >
              Get Started Now
            </Link>
            <Link 
              href="/login" 
              className="px-12 py-5 bg-[#161b22] border border-white/10 hover:border-emerald-500/50 rounded-2xl font-black tracking-wide transition-all text-sm"
            >
              Sign In to Account
            </Link>
          </div>

          {/* Feature Preview Tags */}
          <div className="mt-20 grid grid-cols-2 md:grid-cols-4 gap-8 opacity-40 grayscale group-hover:grayscale-0 transition-all">
             <div className="flex flex-col items-center gap-2">
                <div className="text-2xl font-bold">99%</div>
                <div className="text-[10px] uppercase tracking-widest font-bold text-gray-500">Reliability</div>
             </div>
             <div className="flex flex-col items-center gap-2">
                <div className="text-2xl font-bold">24h</div>
                <div className="text-[10px] uppercase tracking-widest font-bold text-gray-500">Fast Delivery</div>
             </div>
             <div className="flex flex-col items-center gap-2">
                <div className="text-2xl font-bold">100%</div>
                <div className="text-[10px] uppercase tracking-widest font-bold text-gray-500">Verified</div>
             </div>
             <div className="flex flex-col items-center gap-2">
                <div className="text-2xl font-bold">Free</div>
                <div className="text-[10px] uppercase tracking-widest font-bold text-gray-500">Consultation</div>
             </div>
          </div>
        </div>

        {/* Footer */}
        <footer className="py-10 border-t border-white/5 text-center">
          <p className="text-[10px] text-gray-700 font-bold uppercase tracking-widest">
            © 2024 ZweToe Pharmacy. All rights reserved.
          </p>
        </footer>
      </div>
    </main>
  );
}