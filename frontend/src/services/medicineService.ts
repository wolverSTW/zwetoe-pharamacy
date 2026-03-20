import axiosInstance from "@/lib/axios";

/**
 * Service to handle all Medicine related API calls
 */
export const medicineService = {
  // 1. Fetch all medicines
  getAll: async () => {
    try {
      const { data } = await axiosInstance.get("/medicines");
      return data.data || data; 
    } catch (error: any) {
      console.error("Medicine Service Error Details:", {
        message: error.message,
        response: error.response?.data,
        status: error.response?.status
      });
      throw error.response?.data || { message: error.message || "Failed to fetch medicines" };
    }
  },

  // 2. Fetch single medicine
  getById: async (id: string | number) => {
    try {
      const { data } = await axiosInstance.get(`/medicines/${id}`);
      return data.data || data;
    } catch (error: any) {
      throw error.response?.data || { message: "Medicine not found" };
    }
  },

  // 3. Search medicines
  search: async (query: string) => {
    try {
      const { data } = await axiosInstance.get(`/search?q=${query}`);
      return data.data;
    } catch (error: any) {
      throw error.response?.data || { message: "Search failed" };
    }
  }
};