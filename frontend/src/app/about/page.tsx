"use client";

import Link from "next/link";

export default function AboutPage() {
  const stats = [
    { label: "Years Experience", value: "5+" },
    { label: "Trusted Customers", value: "25k+" },
    { label: "Genuine Medicines", value: "100%" },
    { label: "Founder Background", value: "MSc Nursing" },
  ];

  const values = [
    {
      title: "Genuine Quality",
      desc: "We source directly from certified manufacturers to ensure 100% authentic medicines.",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
      ),
    },
    {
      title: "Fast Delivery",
      desc: "Our dedicated logistics team ensures your health needs reach your doorstep in no time.",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
      ),
    },
    {
      title: "Expert Advice",
      desc: "Professional guidance from our medical experts to ensure safe medication.",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="m12 14 4-4"/><path d="m3.34 19 9.39-9.39a2.5 2.5 0 0 1 3.54 3.54L6.88 22.54a1.5 1.5 0 0 1-1.06.46H2v-3.82a1.5 1.5 0 0 1 .46-1.06l9.39-9.39"/><path d="m16 2 6 6"/></svg>
      ),
    },
  ];

  return (
    <main className="min-h-screen bg-[#0d1117] text-white font-sans overflow-hidden">
      {/* --- Hero Section --- */}
      <section className="relative py-20 px-6">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-125 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none" />
        
        <div className="container mx-auto text-center relative z-10">
          <h1 className="text-4xl md:text-6xl font-black tracking-tight uppercase mb-6">
            Empowering Your <span className="text-emerald-500">Health</span>
          </h1>
          <p className="max-w-2xl mx-auto text-gray-400 text-sm md:text-base font-medium leading-relaxed uppercase tracking-wider">
            ZweToe Pharmacy is dedicated to providing accessible, affordable, and authentic 
            healthcare solutions since 2021.
          </p>
        </div>
      </section>

      {/* --- Stats Section --- */}
      <section className="py-12 border-y border-white/5 bg-[#161b22]/30">
        <div className="container mx-auto px-6">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <h2 className="text-2xl md:text-3xl font-black text-emerald-500 mb-1 tracking-tight uppercase">{stat.value}</h2>
                <p className="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em]">{stat.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* --- Founders Expertise Section --- */}
      <section className="container mx-auto px-6 py-20">
        <div className="bg-[#161b22] border border-white/5 rounded-[40px] p-8 md:p-16 relative overflow-hidden group">
          <div className="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 blur-[100px] rounded-full pointer-events-none" />
          
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-5xl font-black uppercase tracking-tighter mb-4">
              Our <span className="text-emerald-500">Founding</span> Team
            </h2>
            <p className="text-gray-500 text-[10px] font-black uppercase tracking-[0.4em]">Expertise meets Innovation</p>
          </div>

          <div className="grid lg:grid-cols-2 gap-12 relative z-10">
            
            {/* Medical Founder (MSc Nursing) */}
            <div className="flex flex-col items-center lg:items-start space-y-6 p-8 rounded-4xl bg-white/5 border border-white/5 transition-all hover:border-emerald-500/30">
              <div className="w-20 h-20 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </div>
              <div className="text-center lg:text-left">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-4">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Medical Leadership</span>
                </div>
                <h3 className="text-xl md:text-2xl font-black uppercase mb-4 leading-tight text-white">
                  MSc <span className="text-emerald-500">Nursing</span> Professional
                </h3>
                <p className="text-gray-400 text-xs md:text-sm leading-relaxed  tracking-wide font-medium">
                  ZweToe Pharmacy is established by a highly experienced healthcare professional with an MSc in Nursing. We prioritize your well-being by meticulously selecting every medicine in accordance with strict medical standards.
                </p>
              </div>
            </div>

            {/* Tech Founder (MSc CS) */}
            <div className="flex flex-col items-center lg:items-start space-y-6 p-8 rounded-4xl bg-white/5 border border-white/5 transition-all hover:border-emerald-500/30">
              <div className="w-20 h-20 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
              </div>
              <div className="text-center lg:text-left">
                <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-4">
                  <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" />
                  <span className="text-[9px] font-black text-emerald-500 uppercase tracking-widest">Digital Innovation</span>
                </div>
                <h3 className="text-xl md:text-2xl font-black uppercase mb-4 leading-tight text-white">
                  MSc <span className="text-emerald-500">Computer Science</span> Candidate
                </h3>
                <p className="text-gray-400 text-xs md:text-sm leading-relaxed tracking-wide font-medium">
                  Our digital platform is developed and managed by our co-founder, an MSc Computer Science candidate. We are committed to integrating advanced technology to provide you with the most efficient and reliable healthcare services.
                </p>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* --- Mission Section with Shield Decoration --- */}
      <section className="py-24 px-6 bg-emerald-500/5 relative">
        <div className="container mx-auto max-w-4xl text-center">
          
          {/* Icon Position (Top Center) */}
          <div className="flex justify-center items-center relative group mb-20">
            <div className="relative">
              <div className="absolute inset-0 bg-emerald-500/20 blur-[60px] rounded-full group-hover:bg-emerald-500/40 transition-all duration-700" />
              <div className="relative w-56 h-56 bg-[#161b22] border border-white/10 rounded-[50px] flex items-center justify-center shadow-2xl transition-transform duration-700 group-hover:scale-105">
                <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1" className="text-emerald-500">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                  <path d="M12 8v8M8 12h8" strokeWidth="2" strokeLinecap="round"/>
                </svg>
              </div>
              
              {/* Floating Decorative Plus */}
              <div className="absolute -top-4 -right-4 w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg animate-bounce">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3">
                  <path d="M12 2v20M2 12h20"/>
                </svg>
              </div>
            </div>
          </div>

          {/* Texts (Centered) */}
          <div className="space-y-16">
            <div className="max-w-2xl mx-auto">
              <h2 className="text-3xl font-black uppercase tracking-tight mb-4">Our Mission</h2>
              <div className="w-12 h-1 bg-emerald-500 mx-auto mb-6 rounded-full" />
              <p className="text-gray-400 text-sm md:text-base leading-8 uppercase tracking-wide">
                To be the most trusted partner in your health journey by delivering 
                excellence through genuine products, professional care, and 
                technological innovation in every service we provide.
              </p>
            </div>

            <div className="max-w-2xl mx-auto pt-12 border-t border-white/10">
              <h2 className="text-3xl font-black uppercase tracking-tight mb-4 text-emerald-500">Our Vision</h2>
              <div className="w-12 h-1 bg-white/20 mx-auto mb-6 rounded-full" />
              <p className="text-gray-400 text-sm md:text-base leading-8 uppercase tracking-wide">
                To create a healthier society where every individual has immediate 
                access to reliable medical resources at the click of a button, 
                anytime and anywhere.
              </p>
            </div>
          </div>

        </div>
      </section>

      {/* --- CTA Section --- */}
      <section className="py-24 text-center">
        <div className="container mx-auto px-6">
          <h2 className="text-2xl md:text-3xl font-black uppercase tracking-tighter mb-8">Trust us with your health journey</h2>
          <Link 
            href="/products" 
            className="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg shadow-emerald-500/20"
          >
            Browse Catalogue
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
          </Link>
        </div>
      </section>
    </main>
  );
}