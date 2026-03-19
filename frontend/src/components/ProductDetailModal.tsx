"use client";
import { useState } from "react";
import { useCart } from "@/context/CartContext";
import { toast } from "react-hot-toast";
import { getImageUrl } from "@/utils/imageHelper";

export default function ProductDetailModal({ item, onClose }: any) {
  const [quantity, setQuantity] = useState(1);
  const { addToCart } = useCart();

  const handleIncrease = () => {
    if (quantity < item.stock_quantity) {
      setQuantity(prev => prev + 1);
    }
  };

  const handleDecrease = () => {
    if (quantity > 1) {
      setQuantity(prev => prev - 1);
    }
  };

  const handleCartAction = (e: React.MouseEvent) => {
    e.stopPropagation();

    console.log("Add to Cart Clicked for:", item.name);
    
    try {
      addToCart(item, quantity);
      toast.success(`${item.name} added to cart!`);
      onClose();
    } catch (error) {
      console.error("Cart Action Error:", error);
      toast.error("Could not add to cart");
    }
  };

  const totalPrice = Math.floor(item.price) * quantity;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div 
        className="absolute inset-0 bg-[#0a0c10]/80 backdrop-blur-md" 
        onClick={onClose}
      ></div>

      <div className="relative bg-[#161b22] border border-white/10 w-full max-w-2xl rounded-3xl overflow-hidden shadow-2xl flex flex-col md:flex-row animate-in fade-in zoom-in duration-300">
        
        <button 
          onClick={onClose} 
          className="absolute top-4 right-4 z-10 p-2 bg-black/20 hover:bg-black/40 rounded-full text-white transition-all group"
        >
          <svg className="transition-transform group-hover:rotate-90" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>

        {/* Left: Image Side */}
        <div className="w-full md:w-1/2 bg-white flex items-center justify-center p-12">
          <img 
            src={getImageUrl(item.image)} 
            alt={item.name}
            width="300" 
            height="300" 
            className="max-h-64 object-contain hover:scale-105 transition-transform duration-500"
            onError={(e) => {
              (e.target as HTMLImageElement).src = "https://placehold.co/400x400?text=No+Image+Found";
            }}
          />
        </div>

        {/* Right: Details Side */}
        <div className="w-full md:w-1/2 p-8 flex flex-col justify-center bg-black/10 backdrop-blur-sm">
          <p className="text-emerald-500 text-[10px] font-black uppercase tracking-[0.3em] mb-2">
            {item.category?.name || "General"}
          </p>
          <h2 className="text-2xl font-black text-white mb-6 leading-tight tracking-tight">
            {item.name}
          </h2>
          
          <div className="bg-[#0d1117]/80 border border-white/5 rounded-2xl p-4 mb-6 backdrop-blur-sm">
            <div className="flex items-center justify-between mb-4">
              <span className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Quantity</span>
              <span className="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-full border border-emerald-500/20">
                Stock: {item.stock_quantity}
              </span>
            </div>
            
            <div className="flex items-center justify-between">
              <div className="flex items-center bg-[#161b22] border border-white/10 rounded-xl p-1">
                <button 
                  onClick={handleDecrease}
                  className="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-all active:scale-90"
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
                <span className="w-10 text-center font-black text-lg text-white select-none">{quantity}</span>
                <button 
                  onClick={handleIncrease}
                  disabled={quantity >= item.stock_quantity}
                  className="w-10 h-10 flex items-center justify-center text-emerald-500 hover:bg-emerald-500/10 rounded-lg transition-all active:scale-90"
                >
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                </button>
              </div>

              <div className="text-right">
                <span className="block text-[10px] font-bold text-gray-500 uppercase mb-1">Subtotal</span>
                <span className="text-xl font-black text-white tabular-nums">
                  {totalPrice.toLocaleString()} <span className="text-xs text-emerald-500 ml-1">MMK</span>
                </span>
              </div>
            </div>
          </div>

          <button 
            onClick={handleCartAction}
            disabled={item.stock_quantity <= 0}
            className="w-full py-4 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-800 disabled:text-gray-500 text-[#0d1117] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-emerald-500/10 transition-all active:scale-[0.97] flex items-center justify-center gap-2"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
            Add {quantity > 1 ? `(${quantity})` : ""} to Cart
          </button>
        </div>
      </div>
    </div>
  );
}