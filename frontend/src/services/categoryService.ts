import axiosInstance from "@/lib/axios";

export const categoryService = {
  getAll: async () => {
    try {
      const { data } = await axiosInstance.get("/categories");
      return data; 
    } catch (error: any) {
      throw error.response?.data || { message: "Failed to fetch categories" };
    }
  }
};