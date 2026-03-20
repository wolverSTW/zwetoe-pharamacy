"use client";
import { useState } from "react";
import { useCart } from "@/context/CartContext";
import { toast } from "react-hot-toast";
import { getImageUrl } from "@/utils/imageHelper";
import Link from "next/link";
import * as Icons from "lucide-react";

export default function ProductDetailModal({ item, onClose, isGuest = false }: any) {
  const [quantity, setQuantity] = useState(1);
  const { addToCart: addToCartContext } = useCart();

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
    try {
      addToCartContext(item, quantity);
      toast.success(`${item.name} added to cart!`, {
        style: { background: '#10b981', color: '#fff', fontWeight: 'bold' }
      });
      onClose();
    } catch (error) {
      console.error("Cart Action Error:", error);
      toast.error("Could not add to cart");
    }
  };

  const totalPrice = Math.floor(item.sell_price || item.price || 0) * quantity;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div
        className="absolute inset-0 bg-[#0a0c10]/90 backdrop-blur-xl"
        onClick={onClose}
      ></div>

      <div className="relative bg-[#0d1117] border border-white/10 w-full max-w-4xl rounded-[2.5rem] overflow-hidden shadow-[0_50px_100px_rgba(0,0,0,0.8)] flex flex-col md:flex-row animate-in fade-in zoom-in duration-500">

        {/* Close Button */}
        <button
          onClick={onClose}
          className="absolute top-6 right-6 z-20 w-10 h-10 bg-white/5 hover:bg-emerald-500 border border-white/10 hover:border-emerald-400 rounded-full text-white hover:text-[#0d1117] transition-all flex items-center justify-center group"
        >
          <Icons.X className="w-5 h-5 transition-transform group-hover:rotate-90" strokeWidth={3} />
        </button>

        {/* Left: Image Canvas */}
        <div className="w-full md:w-5/12 bg-white flex items-center justify-center p-8 relative min-h-[400px]">
          <div className="absolute inset-0 bg-linear-to-br from-gray-50/50 to-transparent"></div>
          <img
            src={getImageUrl(item.image)}
            alt={item.name}
            className="max-h-[450px] w-full object-contain relative z-10 drop-shadow-2xl hover:scale-105 transition-transform duration-700"
            onError={(e) => {
              (e.target as HTMLImageElement).src = "https://placehold.co/600x600?text=Medicine+Visual+Missing";
            }}
          />
          {/* Visual Texture */}
          <div className="absolute top-8 left-8">
            <h3 className="text-[60px] font-black text-gray-100 opacity-20 select-none leading-none tracking-tighter">ZWE<br />TOE</h3>
          </div>
        </div>

        {/* Right: Details Section */}
        <div className="w-full md:w-7/12 p-10 md:p-14 flex flex-col justify-center bg-linear-to-b from-[#161b22] to-[#0d1117] relative">

          <div className="mb-10">
            <div className="flex items-center gap-3 mb-4 min-h-[1.5rem]">
                {item.stock_quantity > 0 && (
                  <span className="flex items-center gap-1.5 text-[9px] font-black text-gray-400 uppercase tracking-widest">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Available Stock
                  </span>
                )}
            </div>

            <h2 className="text-4xl md:text-5xl font-black text-white leading-[1.1] tracking-tight uppercase mb-4">
              {item.name}
            </h2>
          </div>

          {/* Transactional Box */}
          <div className="bg-[#0d1117]/50 border border-white/5 rounded-[2rem] p-8 mb-10 backdrop-blur-sm shadow-inner">
            {isGuest ? (
              <div className="flex flex-col items-center text-center py-6 space-y-5">
                <div className="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 border border-emerald-500/20 shadow-xl">
                  <Icons.LockKeyhole className="w-7 h-7" />
                </div>
                <div className="space-y-2">
                  <p className="text-lg font-black text-white uppercase tracking-widest">Membership Locked</p>
                  <p className="text-[10px] font-bold text-gray-600 uppercase tracking-widest max-w-[200px] leading-relaxed">Please sign in to access clinical pricing and inventory controls</p>
                </div>
              </div>
            ) : (
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div className="space-y-3">
                  <span className="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] pl-1">Adjust Quantity</span>
                  <div className="flex items-center bg-[#161b22] border border-white/10 rounded-2xl p-1.5 shadow-2xl">
                    <button
                      onClick={handleDecrease}
                      className="w-12 h-12 flex items-center justify-center text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all active:scale-90"
                    >
                      <Icons.Minus className="w-5 h-5" strokeWidth={3} />
                    </button>
                    <span className="w-14 text-center font-black text-2xl text-white tabular-nums select-none">{quantity}</span>
                    <button
                      onClick={handleIncrease}
                      disabled={quantity >= item.stock_quantity}
                      className="w-12 h-12 flex items-center justify-center text-emerald-500 hover:bg-emerald-500/10 rounded-xl transition-all active:scale-90"
                    >
                      <Icons.Plus className="w-5 h-5" strokeWidth={3} />
                    </button>
                  </div>
                </div>

                <div className="text-right md:pr-4">
                  <span className="block text-[10px] font-black text-gray-500 uppercase mb-2 tracking-[0.3em] leading-none">Net Total</span>
                  <div className="flex flex-col items-end">
                    <span className="text-4xl font-black text-white tabular-nums tracking-tighter leading-none">
                      {totalPrice.toLocaleString()}
                    </span>
                    <span className="text-md text-emerald-500 font-black uppercase tracking-[0.1em] mt-3">MMK</span>
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Action Area */}
          <div className="flex flex-col gap-4">
            {isGuest ? (
              <div className="flex flex-col sm:flex-row gap-4">
                <Link
                  href="/login"
                  className="flex-1 py-6 bg-emerald-600 hover:bg-emerald-500 text-[#0d1117] font-black uppercase tracking-[0.2em] text-xs rounded-2xl shadow-xl shadow-emerald-500/20 transition-all active:scale-[0.97] flex items-center justify-center gap-3 group"
                >
                  <Icons.UserCircle className="w-5 h-5 group-hover:scale-110 transition-transform" />
                  Sign In to Purchase
                </Link>
                <Link
                  href="/register"
                  className="px-10 py-6 bg-white/5 border border-white/10 hover:border-emerald-500/50 text-white font-black uppercase tracking-[0.2em] text-[10px] rounded-2xl transition-all flex items-center justify-center"
                >
                  Register Now
                </Link>
              </div>
            ) : (
              <button
                onClick={handleCartAction}
                disabled={item.stock_quantity <= 0}
                className="w-full py-7 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-800 disabled:text-gray-500  text-[#0d1117] font-black tracking-[0.1em] text-sm rounded-2xl shadow-2xl shadow-emerald-500/30 transition-all active:scale-[0.98] flex items-center justify-center gap-4 group"
              >
                <Icons.ShoppingBag className="w-6 h-6 group-hover:-translate-y-1 transition-transform" strokeWidth={2.5} />
                Add to Cart
              </button>
            )}

            <p className="text-[9px] text-center text-gray-600 font-bold uppercase tracking-[0.4em] mt-2 opacity-50">
              Secured by ZweToe Pharmaceutical Network
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}