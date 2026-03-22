"use client";

import { usePathname } from "next/navigation";
import { AuthProvider } from "@/context/AuthContext";
import { Toaster } from "react-hot-toast";
import Navbar from "@/components/Navbar";
import { CartProvider } from "../context/CartContext";
import AIChatbot from "@/components/AIChatbot";
import "./globals.css";

export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();

  const isCartPage = pathname === "/cart";

  return (
    <html lang="en">
      <body className="bg-[#0a0c10] text-white"> 
        <Toaster 
          position="bottom-right"
          toastOptions={{
            duration: 3000,
            style: {
              background: '#161b22',
              color: '#fff',
              border: '1px solid rgba(255,255,255,0.05)',
            },
          }}
        />
        
        <AuthProvider>
          <main className="min-h-screen">
            <CartProvider>
              {!isCartPage && <Navbar />}
              
              {children}
              
              <AIChatbot />
            </CartProvider>
          </main>
        </AuthProvider>
      </body>
    </html>
  );
}