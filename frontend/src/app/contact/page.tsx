"use client";

import Link from "next/link";

export default function ContactPage() {
  const contactInfo = [
    {
      title: "Call Us",
      value: "+95 9 964 777 223",
      desc: "Mon - Sat (9am - 5pm)",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      ),
    },
    {
      title: "Location",
      value: "Padigon, Thegon Tsp, Bago West, Myanmar",
      desc: "Your Trusted Local Pharmacy",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      ),
    },
    {
      title: "Consultation",
      value: "Speak with MSc Nursing",
      desc: "Professional Health Advice",
      icon: (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
      ),
    },
  ];

  return (
    <main className="min-h-screen bg-[#0d1117] text-white font-sans overflow-hidden">
      {/* --- Header Section --- */}
      <section className="relative py-24 px-6 text-center">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-100 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none" />
        <div className="relative z-10">
          <h1 className="text-4xl md:text-6xl font-black tracking-tight uppercase mb-6">
            Get In <span className="text-emerald-500">Touch</span>
          </h1>
          <p className="max-w-xl mx-auto text-gray-400 text-sm md:text-base font-medium leading-relaxed uppercase tracking-wider">
            Have questions about medicines or tech? Our team is here to help you.
          </p>
        </div>
      </section>

      {/* --- Contact Cards --- */}
      <section className="container mx-auto px-6 py-12">
        <div className="grid md:grid-cols-3 gap-8">
          {contactInfo.map((item, index) => (
            <div 
              key={index} 
              className="bg-[#161b22] border border-white/5 p-8 rounded-[40px] hover:border-emerald-500/30 transition-all duration-500 group flex flex-col items-center text-center"
            >
              <div className="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-6 group-hover:scale-110 transition-transform">
                {item.icon}
              </div>
              <h3 className="text-gray-500 text-[10px] font-black uppercase tracking-[0.3em] mb-2">{item.title}</h3>
              <p className="text-xl font-bold mb-2 uppercase tracking-tight">{item.value}</p>
              <p className="text-gray-400 text-xs font-medium uppercase tracking-widest">{item.desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* --- Contact Form & Map Placeholders --- */}
      <section className="container mx-auto px-6 py-24">
        <div className="bg-[#161b22] border border-white/5 rounded-[50px] p-8 md:p-16 flex flex-col lg:flex-row gap-16">
          
          {/* Form Side */}
          <div className="flex-1">
            <h2 className="text-3xl font-black uppercase tracking-tight mb-8">Send a Message</h2>
            <form className="space-y-6">
              <div className="grid md:grid-cols-2 gap-6">
                <div className="space-y-2">
                  <label className="text-[10px] font-black uppercase tracking-widest text-emerald-500 ml-2">Name</label>
                  <input type="text" placeholder="Your Name" className="w-full bg-[#0d1117] border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-emerald-500 transition-colors text-sm uppercase font-medium" />
                </div>
                <div className="space-y-2">
                  <label className="text-[10px] font-black uppercase tracking-widest text-emerald-500 ml-2">Email</label>
                  <input type="email" placeholder="Your Email" className="w-full bg-[#0d1117] border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-emerald-500 transition-colors text-sm uppercase font-medium" />
                </div>
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-black uppercase tracking-widest text-emerald-500 ml-2">Message</label>
                <textarea rows={5} placeholder="How can we help you?" className="w-full bg-[#0d1117] border border-white/10 rounded-2xl px-6 py-4 outline-none focus:border-emerald-500 transition-colors text-sm uppercase font-medium resize-none"></textarea>
              </div>
              <button className="bg-emerald-600 hover:bg-emerald-500 text-white w-full py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                Send Message
              </button>
            </form>
          </div>

          {/* Connect Side */}
          <div className="lg:w-1/3 flex flex-col justify-center">
            <div className="bg-emerald-500/5 rounded-3xl p-8 border border-emerald-500/10">
              <h3 className="text-xl font-black uppercase tracking-tight mb-6">Social Connect</h3>
              <div className="space-y-4">
                <Link href="#" className="flex items-center gap-4 text-gray-400 hover:text-emerald-500 transition-colors group">
                  <div className="w-10 h-10 bg-[#0d1117] rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                  </div>
                  <span className="text-xs font-black uppercase tracking-widest">Facebook</span>
                </Link>
                <Link href="#" className="flex items-center gap-4 text-gray-400 hover:text-emerald-500 transition-colors group">
                  <div className="w-10 h-10 bg-[#0d1117] rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                  </div>
                  <span className="text-xs font-black uppercase tracking-widest">Twitter / X</span>
                </Link>
                <Link href="#" className="flex items-center gap-4 text-gray-400 hover:text-emerald-500 transition-colors group">
                  <div className="w-10 h-10 bg-[#0d1117] rounded-xl flex items-center justify-center group-hover:bg-emerald-500/20 transition-colors">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                  </div>
                  <span className="text-xs font-black uppercase tracking-widest">Instagram</span>
                </Link>
              </div>
            </div>
          </div>

        </div>
      </section>
    </main>
  );
}