"use client";

import { useState } from "react";
import { Pill } from "lucide-react";

interface ProductImageProps {
  src?: string | null;
  alt: string;
  sizes: string;
  className?: string;
  priority?: boolean;
  iconClassName?: string;
}

export default function ProductImage({
  src,
  alt,
  sizes,
  className = "object-contain",
  priority = false,
  iconClassName = "w-12 h-12 text-gray-200",
}: ProductImageProps) {
  const [hasError, setHasError] = useState(false);

  if (!src || hasError) {
    return (
      <div className="absolute inset-0 flex items-center justify-center">
        <Pill className={iconClassName} strokeWidth={1.5} aria-hidden="true" />
      </div>
    );
  }

  return (
    <img
      src={src}
      alt={alt}
      className={`${className} w-full h-full object-contain`}
      onError={() => setHasError(true)}
    />
  );
}
