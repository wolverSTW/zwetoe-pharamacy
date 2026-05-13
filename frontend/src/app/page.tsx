"use client";

import { useAuth } from "@/context/AuthContext";
import GuestHomePage from "@/components/features/home/GuestHomePage";
import RegisteredHomePage from "@/components/features/home/RegisteredHomePage";

export default function HomePage() {
  const { user, loading } = useAuth();

  if (user) {
    return <RegisteredHomePage user={user} />;
  }

  return <GuestHomePage isHydrating={loading} />;
}
