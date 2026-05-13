"use client";

import { useEffect, useState } from "react";
import dynamic from "next/dynamic";
import { usePathname } from "next/navigation";
import { Toaster } from "react-hot-toast";
import { AuthProvider } from "@/context/AuthContext";
import { CartProvider } from "@/context/CartContext";
import Navbar from "@/components/layout/Navbar";

const AIChatbot = dynamic(() => import("@/components/ui/AIChatbot"), {
  ssr: false,
  loading: () => null,
});

export default function ClientShell({
  children,
}: {
  children: React.ReactNode;
}) {
  const pathname = usePathname();
  const [showChatbot, setShowChatbot] = useState(false);

  useEffect(() => {
    const scheduleChatbot = () => setShowChatbot(true);

    if (typeof window !== "undefined" && "requestIdleCallback" in window) {
      const idleId = window.requestIdleCallback(scheduleChatbot, { timeout: 1500 });
      return () => window.cancelIdleCallback(idleId);
    }

    const timeoutId = setTimeout(scheduleChatbot, 1200);
    return () => clearTimeout(timeoutId);
  }, []);

  const isCartPage = pathname === "/cart";

  return (
    <>
      <Toaster
        position="bottom-right"
        toastOptions={{
          duration: 3000,
          style: {
            background: "#161b22",
            color: "#fff",
            border: "1px solid rgba(255,255,255,0.05)",
          },
        }}
      />

      <AuthProvider>
        <CartProvider>
          <main className="min-h-screen">
            {!isCartPage && <Navbar />}
            {children}
            {showChatbot && <AIChatbot />}
          </main>
        </CartProvider>
      </AuthProvider>
    </>
  );
}
