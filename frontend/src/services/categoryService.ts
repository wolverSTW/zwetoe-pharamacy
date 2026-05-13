import axiosInstance from "@/lib/axios";
import { AxiosError } from "axios";
import { Category } from "@/types/store";

const CATEGORY_CACHE_TTL = 5 * 60 * 1000;

let categoriesCache: Category[] | null = null;
let categoriesCacheTime = 0;

export const categoryService = {
  getAll: async (): Promise<Category[]> => {
    if (categoriesCache && Date.now() - categoriesCacheTime < CATEGORY_CACHE_TTL) {
      return categoriesCache;
    }

    try {
      const { data } = await axiosInstance.get("/categories");
      const normalizedData = data.data || data;
      categoriesCache = normalizedData;
      categoriesCacheTime = Date.now();
      return normalizedData;
    } catch (error) {
      const axiosErr = error as AxiosError<{ message?: string }>;
      const errorDetails = {
        message: axiosErr.message || "Timeout / Network Request Failed",
        code: axiosErr.code || "No Error Code",
        status: axiosErr.response?.status || "No Status",
        url: axiosErr.config?.url || "Unknown URL"
      };
      console.error("Category Service Fatal Error:", JSON.stringify(errorDetails, null, 2));
      throw axiosErr.response?.data || { message: "Failed to fetch categories" };
    }
  }
};
