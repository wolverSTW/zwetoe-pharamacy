import axiosInstance from "@/lib/axios";

export const categoryService = {
  getAll: async () => {
    try {
      const { data } = await axiosInstance.get("/categories");
      return data.data || data; 
    } catch (error: any) {
      console.error("Category Service Fatal Error:", error.message || error);
      throw error.response?.data || { message: "Failed to fetch categories" };
    }
  }
};