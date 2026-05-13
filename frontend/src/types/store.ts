export interface Category {
  id: number;
  name: string;
}

export interface Address {
  house_number: string;
  street: string;
  town: string;
  township: string;
  region: string;
  phone: string;
  [key: string]: string;
}

export interface User {
  id: number;
  email: string;
  name?: string;
  customer_name?: string;
  username?: string;
  phone?: string;
  status?: string;
  address?: Address | null;
}

export interface Medicine {
  id: number;
  name: string;
  generic_name?: string | null;
  sell_price?: number | null;
  price?: number | null;
  image?: string | null;
  image_url?: string | null;
  stock_quantity: number;
  category?: Category | null;
}

export interface CartProduct {
  id: number;
  name: string;
  price?: number | null;
  sell_price?: number | null;
  image?: string | null;
  image_url?: string | null;
  stock_quantity?: number;
}

export interface CartItem extends CartProduct {
  quantity: number;
}
