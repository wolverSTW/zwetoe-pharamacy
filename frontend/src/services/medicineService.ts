import axiosInstance from "@/lib/axios";
import { Medicine } from "@/types/store";

const MEDICINE_CACHE_TTL = 5 * 60 * 1000;

let medicinesCache: Medicine[] | null = null;
let medicinesCacheTime = 0;

/**
 * Service to handle all Medicine related API calls
 */
export const medicineService = {
  // 1. Fetch all medicines
  getAll: async (): Promise<Medicine[]> => {
    if (medicinesCache && Date.now() - medicinesCacheTime < MEDICINE_CACHE_TTL) {
      return medicinesCache;
    }

    try {
      const { data } = await axiosInstance.get("/medicines");
      const normalizedData = data.data || data;
      medicinesCache = normalizedData;
      medicinesCacheTime = Date.now();
      return normalizedData;
    } catch (error: any) {
      const errorDetails = {
        message: error.message || "Timeout / Network Request Failed",
        code: error.code || "No Error Code",
        status: error.response?.status || "No Status",
        url: error.config?.url || "Unknown URL"
      };
      
      console.error("Medicine Service Fatal Error:", JSON.stringify(errorDetails, null, 2));
      throw error.response?.data || { message: error.message || "Failed to fetch medicines" };
    }
  },

  // 2. Fetch single medicine
  getById: async (id: string | number): Promise<Medicine> => {
    try {
      const { data } = await axiosInstance.get(`/medicines/${id}`);
      return data.data || data;
    } catch (error: any) {
      throw error.response?.data || { message: "Medicine not found" };
    }
  },

  // 3. Search medicines
  search: async (query: string): Promise<Medicine[]> => {
    try {
      const { data } = await axiosInstance.get(`/search?q=${query}`);
      return data.data;
    } catch (error: any) {
      throw error.response?.data || { message: "Search failed" };
    }
  }
};
