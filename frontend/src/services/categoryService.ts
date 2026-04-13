import axiosInstance from "@/lib/axios";

export const categoryService = {
  getAll: async () => {
    try {
      const { data } = await axiosInstance.get("/categories");
      return data.data || data; 
    } catch (error: any) {
      const errorDetails = {
        message: error.message || "Timeout / Network Request Failed",
        code: error.code || "No Error Code",
        status: error.response?.status || "No Status",
        url: error.config?.url || "Unknown URL"
      };
      console.error("Category Service Fatal Error:", JSON.stringify(errorDetails, null, 2));
      throw error.response?.data || { message: "Failed to fetch categories" };
    }
  }
};