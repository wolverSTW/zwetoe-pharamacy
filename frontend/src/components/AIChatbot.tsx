"use client";
import { useState, useRef, useEffect } from "react";

// Professional AI Robot Icon SVG
const BotIcon = () => (
  <div className="w-9 h-9 rounded-xl bg-linear-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path 
        d="M12 2V4M5 8V7C5 5.34315 6.34315 4 8 4H16C17.6569 4 19 5.34315 19 7V8" 
        stroke="white" strokeWidth="1.5" strokeLinecap="round" 
      />
      <rect x="4" y="8" width="16" height="12" rx="3" stroke="white" strokeWidth="1.5" />
      <circle cx="8.5" cy="13.5" r="1.5" fill="white" className="animate-pulse" />
      <circle cx="15.5" cy="13.5" r="1.5" fill="white" className="animate-pulse" />
      <path d="M9 17H15" stroke="white" strokeWidth="1.5" strokeLinecap="round" opacity="0.6" />
      <path d="M20 12V14M4 12V14" stroke="white" strokeWidth="1.5" strokeLinecap="round" />
    </svg>
  </div>
);

const UserIcon = () => (
  <div className="w-9 h-9 rounded-xl bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />
    </svg>
  </div>
);

export default function AIChatbot() {
  const [isOpen, setIsOpen] = useState(false);
  const [input, setInput] = useState("");
  const [loading, setLoading] = useState(false);
  const [messages, setMessages] = useState([
    { role: "ai", content: "မင်္ဂလာပါ! ZweToe Pharmacy AI Assistant မှ ကြိုဆိုပါတယ်။ ဘာများ ကူညီပေးရမလဲခင်ဗျာ?" }
  ]);

  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTo({
        top: scrollRef.current.scrollHeight,
        behavior: "smooth"
      });
    }
  }, [messages, loading]);

  const handleSend = async () => {
    if (!input.trim() || loading) return;
    const userMsg = { role: "user", content: input };
    setMessages(prev => [...prev, userMsg]);
    setInput("");
    setLoading(true);

    try {
      const res = await fetch("/api/chat", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: input }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || "Failed to connect");
      setMessages(prev => [...prev, { role: "ai", content: data.text }]);
    } catch (err: any) {
      setMessages(prev => [...prev, { role: "ai", content: "စနစ်ချို့ယွင်းချက်ရှိနေပါသည်။ ခဏအကြာမှ ပြန်စမ်းကြည့်ပေးပါ။" }]);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed bottom-6 right-6 z-9999 font-sans selection:bg-emerald-500/30 text-white">
      {/* Floating Toggle Button */}
     <button 
      onClick={() => setIsOpen(!isOpen)}
      className={`group relative w-16 h-16 rounded-2xl shadow-2xl flex items-center justify-center transition-all duration-500 ease-in-out ${
        isOpen ? 'bg-zinc-800 rotate-90 scale-90' : 'bg-emerald-500 hover:scale-110 active:scale-95 shadow-emerald-500/40'
      }`}
    >
      {isOpen ? (
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2.5">
          <line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" />
        </svg>
      ) : (
        <div className="relative group-hover:animate-bounce duration-1000">
          {/* Notification Dot */}
          <span className="absolute -top-3 -right-3 flex h-3 w-3">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
            <span className="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
          </span>

          <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            {/* Robot Antennas */}
            <path d="M12 2V4M8 4H16" stroke="black" strokeWidth="1.5" strokeLinecap="round"/>
            
            {/* Robot Head Body */}
            <rect x="4" y="6" width="16" height="14" rx="3" fill="black" />
            
            {/* Screen / Face Area */}
            <rect x="6" y="8" width="12" height="8" rx="2" fill="#10b981" fillOpacity="0.2" stroke="#10b981" strokeWidth="0.5" />
            
            {/* Animated Eyes */}
            <g className="animate-pulse">
              <circle cx="9" cy="12" r="1.5" fill="#10b981" />
              <circle cx="15" cy="12" r="1.5" fill="#10b981" />
            </g>

            {/* Mouth/Line */}
            <path d="M10 17H14" stroke="#10b981" strokeWidth="1.5" strokeLinecap="round" opacity="0.8" />
            
            {/* Side Bolts */}
            <path d="M2 11V15M22 11V15" stroke="black" strokeWidth="2" strokeLinecap="round" />
          </svg>
        </div>
      )}
    </button>

      {/* Chat Window */}
      {isOpen && (
        <div className="absolute bottom-20 right-0 w-95 md:w-105 h-150 bg-zinc-950/90 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.4)] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-300">
          
          {/* Header */}
          <div className="p-6 bg-linear-to-b from-white/5 to-transparent border-b border-white/5 flex items-center justify-between">
            <div className="flex items-center gap-4">
              <BotIcon />
              <div>
                <h3 className="font-bold text-white text-base tracking-tight leading-none">ZweToe AI</h3>
                <div className="flex items-center gap-1.5 mt-1.5">
                  <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                  <span className="text-[11px] text-zinc-300 font-medium uppercase tracking-wider">Pharmacy Assistant</span>
                </div>
              </div>
            </div>
            <button onClick={() => setMessages([{role:'ai', content:'မင်္ဂလာပါ! ဘာများ ကူညီပေးရမလဲခင်ဗျာ?'}])} className="text-zinc-500 hover:text-white transition-colors">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </button>
          </div>
          
          {/* Messages Section */}
          <div ref={scrollRef} className="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            {messages.map((msg, i) => (
              <div key={i} className={`flex items-start gap-3 ${msg.role === 'user' ? 'flex-row-reverse' : ''} animate-in fade-in slide-in-from-bottom-2 duration-300`}>
                <div className="shrink-0">
                  {msg.role === 'user' ? <UserIcon /> : <BotIcon />}
                </div>
                <div className={`px-4 py-3 rounded-2xl text-[14px] leading-relaxed max-w-[80%] shadow-sm ${
                  msg.role === 'user' 
                  ? 'bg-emerald-500 text-black font-medium rounded-tr-none' 
                  : 'bg-zinc-800/80 text-zinc-100 border border-white/5 rounded-tl-none'
                }`}>
                  {msg.content}
                </div>
              </div>
            ))}
            {loading && (
              <div className="flex items-start gap-3">
                <BotIcon />
                <div className="bg-zinc-800/80 px-5 py-3 rounded-2xl rounded-tl-none border border-white/5 flex gap-1 animate-pulse">
                  <span className="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce"></span>
                  <span className="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                  <span className="w-1.5 h-1.5 bg-zinc-500 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                </div>
              </div>
            )}
          </div>

          {/* Input Section */}
          <div className="p-5 bg-linear-to-t from-white/2 to-transparent border-t border-white/5">
            <div className="relative flex items-center bg-zinc-900/50 border border-white/10 rounded-2xl p-1.5 focus-within:border-emerald-500/50 transition-all">
              <input 
                value={input}
                onChange={(e) => setInput(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleSend()}
                placeholder="မေးမြန်းလိုသည်များကို ရိုက်ထည့်ပါ..."
                className="flex-1 bg-transparent px-4 py-2.5 text-sm text-zinc-100 placeholder:text-zinc-500 outline-none"
              />
              <button 
                onClick={handleSend} 
                disabled={loading || !input.trim()}
                className="bg-emerald-500 text-black p-2.5 rounded-xl hover:bg-emerald-400 disabled:opacity-30 transition-all active:scale-90"
              >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
              </button>
            </div>
            <p className="text-[10px] text-zinc-600 mt-3 text-center uppercase tracking-widest font-semibold">ZweToe Intelligent Assistant</p>
          </div>
        </div>
      )}
      
      <style jsx>{`
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
      `}</style>
    </div>
  );
}