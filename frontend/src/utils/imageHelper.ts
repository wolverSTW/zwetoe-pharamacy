export const getImageUrl = (imagePath?: string | null): string | null => {
  if (!imagePath) {
    return null;
  }

  // If it's already a full URL, return it
  if (imagePath.startsWith("http://") || imagePath.startsWith("https://") || imagePath.startsWith("data:")) {
    return imagePath;
  }

  const rawApiUrl = process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api/v1";
  const baseUrl = rawApiUrl.replace(/\/api\/v1\/?$/, "").replace(/\/$/, "");
  
  // Clean the path: replace backslashes and remove leading slash if present
  let cleanedPath = imagePath.replace(/\\/g, "/");
  if (cleanedPath.startsWith("/")) {
    cleanedPath = cleanedPath.substring(1);
  }

  // If the path already includes 'storage/', don't add it again
  if (cleanedPath.startsWith("storage/")) {
    return `${baseUrl}/${cleanedPath}`;
  }

  return `${baseUrl}/storage/${cleanedPath}`;
};
