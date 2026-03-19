"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useAuth } from "@/context/AuthContext";
import { useCart } from "@/context/CartContext";

export default function Navbar() {
  const { user, logout } = useAuth();
  const { totalItems } = useCart();
  const pathname = usePathname();

  const displayName = 
    user?.customer_name || user?.name || user?.username || "Customer";

  const guestLinks = [
    { name: "Home", href: "/" },
    { name: "Medicines", href: "/products" },
    { name: "About", href: "/about" },
    { name: "Contact", href: "/contact" },
  ];

  return (
    <nav className="bg-[#0d1117]/90 border-b border-white/5 py-3 px-6 sticky top-0 z-50 backdrop-blur-md shadow-lg">
      <div className="container mx-auto flex items-center justify-between relative"> {/* relative ထည့်ထားတာက absolute center အတွက်ပါ */}
        
        {/* --- Left Side: Logo --- */}
        <div className="flex items-center z-10">
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center text-[#0d1117] font-bold group-hover:bg-emerald-400 transition-all">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="4">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
            </div>
            <h1 className="text-white font-bold text-lg hidden sm:block uppercase tracking-tight">
              ZweToe <span className="text-emerald-500">Pharmacy</span>
            </h1>
          </Link>
        </div>

        {/* --- Center Side: Guest Navigation Links --- */}
        {!user && (
          <div className="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-8 animate-in fade-in slide-in-from-top-2 duration-700">
            {guestLinks.map((link) => {
              const isActive = pathname === link.href;
              return (
                <Link 
                  key={link.href} 
                  href={link.href} 
                  className={`text-[11px] font-bold uppercase tracking-[0.2em] transition-all relative py-1
                    ${isActive ? "text-emerald-500" : "text-gray-400 hover:text-white"}`}
                >
                  {link.name}
                  {isActive && (
                    <span className="absolute -bottom-1 left-0 w-full h-0.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.5)]" />
                  )}
                </Link>
              );
            })}
          </div>
        )}

        {/* --- Right Side: Cart & Auth --- */}
        <div className="flex items-center gap-4 z-10">
          {user && (
            <>
              <Link href="/cart" className={`relative transition-colors ${pathname === '/cart' ? "text-emerald-500" : "text-gray-400 hover:text-white"}`}>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                  <circle cx="9" cy="21" r="1"></circle>
                  <circle cx="20" cy="21" r="1"></circle>
                  <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                {totalItems > 0 && (
                  <span className="absolute -top-2 -right-2 bg-emerald-500 text-[#0d1117] text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-black">
                    {totalItems}
                  </span>
                )}
              </Link>
              <div className="h-4 w-px bg-white/10 mx-1"></div>
            </>
          )}

          {user ? (
            <div className="flex items-center gap-3">
              <div className="hidden sm:flex flex-col items-end leading-none">
                <span className="text-[11px] font-bold text-white uppercase tracking-tight">{displayName}</span>
                <span className="text-[8px] text-emerald-500 font-black uppercase tracking-widest mt-0.5">Member</span>
              </div>
              <div className="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 text-xs font-black uppercase">
                {displayName.charAt(0)}
              </div>
              <button onClick={logout} className="text-red-500 hover:text-red-400 text-[11px] font-bold ml-1 transition-colors uppercase">Logout</button>
            </div>
          ) : (
            <div className="flex items-center gap-3">
              <Link href="/login" className={`text-[12px] font-bold transition-colors ${pathname === '/login' ? 'text-emerald-500' : 'text-white hover:text-emerald-500'}`}>Sign In</Link>
              <Link href="/register" className="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-md text-[10px] font-black tracking-wide transition-all shadow-lg shadow-emerald-900/20 active:scale-95">Register</Link>
            </div>
          )}
        </div>

      </div>
    </nav>
  );
}