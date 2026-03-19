export const getImageUrl = (imagePath: string | null): string => {
  if (!imagePath) return "https://placehold.co/400x400?text=No+Image";

  const rawApiUrl = process.env.NEXT_PUBLIC_API_URL || "";

  const baseUrl = rawApiUrl.replace(/\/api\/v1\/?$/, "").replace(/\/$/, "");

  const cleanedPath = imagePath.replace(/\\/g, '/');

  return `${baseUrl}/storage/${cleanedPath}`;
};