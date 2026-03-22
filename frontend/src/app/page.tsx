"use client";

import { useEffect, useState } from "react";
import GuestHomePage from "@/components/home/GuestHomePage";
import RegisteredHomePage from "@/components/home/RegisteredHomePage";
import Navbar from "@/components/Navbar"; 

export default function HomePage() {
  const [user, setUser] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchUserData = () => {
      try {
        const savedData = localStorage.getItem("user");
        if (savedData && savedData !== "undefined") {
          setUser(JSON.parse(savedData));
        }
      } catch (error) {
        console.error("Error loading user:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchUserData();
  }, []);

  const handleLogout = () => {
    localStorage.removeItem("user");
    window.location.reload();
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#0d1117] flex items-center justify-center">
        <div className="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
      </div>
    );
  }

  return (
    <>
      {/* <Navbar user={user} onLogout={handleLogout} /> */}
      {user ? <RegisteredHomePage user={user} /> : <GuestHomePage />}
    </>
  );
}