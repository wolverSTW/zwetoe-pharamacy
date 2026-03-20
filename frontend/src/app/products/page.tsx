"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { medicineService } from "@/services/medicineService";
import { categoryService } from "@/services/categoryService";
import { getImageUrl } from "@/utils/imageHelper";
import ProductDetailModal from "@/components/ProductDetailModal";
import { useCart } from "@/context/CartContext";
import toast from "react-hot-toast";
import * as Icons from "lucide-react";

export default function MedicinesPage() {
  const { addToCart: addToCartContext } = useCart();
  const [medicines, setMedicines] = useState([]);
  const [filteredMedicines, setFilteredMedicines] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeCategory, setActiveCategory] = useState("All");
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedMedicine, setSelectedMedicine] = useState<any>(null);
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    // Load user for price visibility
    const savedUser = localStorage.getItem("user");
    if (savedUser && savedUser !== "undefined") {
      setUser(JSON.parse(savedUser));
    }

    const fetchData = async () => {
      try {
        const [medData, catData] = await Promise.all([
          medicineService.getAll(),
          categoryService.getAll()
        ]);
        setMedicines(medData);
        setFilteredMedicines(medData);
        setCategories(catData);
      } catch (error) {
        console.error("Fetch Failure:", error);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  useEffect(() => {
    let result = medicines;
    if (activeCategory !== "All") {
      result = result.filter((m: any) => m.category?.name === activeCategory);
    }
    if (searchTerm) {
      result = result.filter((m: any) =>
        m.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        m.generic_name?.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }
    setFilteredMedicines(result);
  }, [searchTerm, medicines, activeCategory]);

  const addToCart = (item: any) => {
    if (!user) {
      toast.error("Please login to purchase items", {
        style: { background: '#161b22', color: '#fff', fontSize: '12px' }
      });
      return;
    }
    addToCartContext(item, 1);
    toast.success(`${item.name} added!`, {
      style: { background: '#10b981', color: '#fff', fontSize: '12px' }
    });
  };

  if (loading) return (
    <div className="min-h-screen bg-[#0d1117] flex items-center justify-center">
      <div className="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
    </div>
  );

  return (
    <main className="min-h-screen bg-[#0d1117] text-white font-sans pt-24 pb-20 selection:bg-emerald-500/20">
      <div className="container mx-auto px-6">
        
        {/* Header Section (Based on Image) */}
        <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-8 mb-16">
          <div className="space-y-1">
            <h1 className="text-4xl font-black tracking-tight leading-none italic uppercase">
              EXPLORE <span className="text-emerald-500">MEDICINES</span>
            </h1>
            {!user && (
              <p className="flex items-center gap-2 text-[10px] font-black text-gray-500 uppercase tracking-widest pl-1">
                <Icons.Lock className="w-3 h-3" />
                Pricing Locked for Guests
              </p>
            )}
          </div>

          <div className="w-full md:max-w-md relative group">
            <Icons.Search className="absolute right-6 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 group-focus-within:text-emerald-500 transition-colors" />
            <input
              type="text"
              placeholder="SEARCH CATALOGUE...."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full bg-[#161b22] border border-white/5 rounded-2xl py-4 pl-8 pr-16 text-xs font-black text-white focus:outline-none focus:border-emerald-500/50 transition-all uppercase tracking-widest"
            />
          </div>
        </div>

        {/* Medicine Nav (Horizontal Cards from Image) */}
        <div className="relative mb-20 no-print">
          <div className="flex overflow-x-auto gap-4 pb-10 no-scrollbar scroll-smooth">
            <button
              onClick={() => setActiveCategory("All")}
              className={`flex-shrink-0 w-44 aspect-square rounded-2xl transition-all flex flex-col items-center justify-center gap-5 border-2
                ${activeCategory === "All" 
                  ? 'bg-emerald-500 border-emerald-400 text-[#0d1117]' 
                  : 'bg-[#161b22] border-white/5 text-gray-400 hover:border-emerald-500/20 shadow-2xl'}`}
            >
               <Icons.LayoutGrid className="w-10 h-10" />
               <span className="text-[11px] font-black uppercase tracking-widest text-center px-4 leading-tight">All Items</span>
            </button>

            {categories.map((cat: any) => (
              <button
                key={cat.id}
                onClick={() => setActiveCategory(cat.name)}
                className={`flex-shrink-0 w-44 aspect-square rounded-2xl transition-all flex flex-col items-center justify-center gap-5 border-2
                  ${activeCategory === cat.name 
                    ? 'bg-emerald-500 border-emerald-400 text-[#0d1117]' 
                    : 'bg-[#161b22] border-white/5 text-gray-400 hover:border-emerald-500/20 shadow-2xl'}`}
              >
                 <div className="opacity-90">{getCategoryIcon(cat.name, "w-10 h-10")}</div>
                 <span className="text-[11px] font-black uppercase tracking-widest text-center px-4 leading-tight">{cat.name}</span>
              </button>
            ))}
          </div>
          {/* Slider Line from Image */}
          <div className="absolute bottom-6 left-0 right-0 h-1 bg-white/5 rounded-full">
            <div className="w-1/4 h-full bg-white/20 rounded-full"></div>
          </div>
        </div>

        {/* Product Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-10">
          {filteredMedicines.map((item: any) => (
            <ProductCard key={item.id} item={item} user={user} onSelect={setSelectedMedicine} onAddToCart={addToCart} />
          ))}
        </div>

        {filteredMedicines.length === 0 && (
          <div className="py-40 text-center bg-[#161b22] border border-white/5 rounded-[4rem] opacity-50">
             <Icons.DatabaseBackup className="w-16 h-16 mx-auto mb-6 text-gray-700" />
             <p className="text-sm font-black uppercase tracking-widest">Medication Catalogue Search failed</p>
          </div>
        )}

      </div>

      {selectedMedicine && (
        <ProductDetailModal
          item={selectedMedicine}
          onClose={() => setSelectedMedicine(null)}
          onAddToCart={addToCart}
          isGuest={!user}
        />
      )}
    </main>
  );
}

function ProductCard({ item, user, onSelect, onAddToCart }: any) {
  const isGuest = !user;
  return (
    <div 
      onClick={() => onSelect(item)}
      className="group bg-[#161b22] border border-white/5 rounded-[2.5rem] overflow-hidden transition-all duration-500 flex flex-col shadow-[0_20px_50px_rgba(0,0,0,0.3)] hover:-translate-y-3 cursor-pointer"
    >
      {/* Product Image Section (White Box from Image) */}
      <div className="relative aspect-square m-5 mb-0 bg-white rounded-[2rem] flex items-center justify-center p-12 overflow-hidden shadow-inner">
        <div className={`absolute top-5 right-5 flex items-center gap-2 px-3.5 py-1.5 rounded-full z-10 border shadow-sm
          ${item.stock_quantity > 0 ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-red-50 border-red-100 text-red-600'}`}>
           <span className={`w-2 h-2 rounded-full animate-pulse ${item.stock_quantity > 0 ? 'bg-emerald-500' : 'bg-red-500'}`}></span>
           <span className="text-[10px] font-black uppercase tracking-widest">{item.stock_quantity > 0 ? 'In Stock' : 'Low Stock'}</span>
        </div>

        {item.image ? (
          <img 
            src={getImageUrl(item.image)} 
            className="max-h-full object-contain group-hover:scale-110 transition-transform duration-1000 ease-out" 
            alt={item.name} 
          />
        ) : (
          <Icons.Pill className="w-20 h-20 text-gray-100" strokeWidth={1} />
        )}
      </div>

      {/* Content Section from Image */}
      <div className="p-8 pt-6 flex flex-col flex-1">
        <div className="mb-8">
          <p className="text-[10px] font-black text-emerald-500/80 uppercase tracking-widest mb-2">{item.category?.name || "Verified Medicine"}</p>
          <h4 className="text-xl font-black text-white tracking-tight uppercase leading-tight line-clamp-2 min-h-[3.5rem]">{item.name}</h4>
          {!isGuest && (
            <p className="text-[11px] text-gray-500 font-bold italic mt-2 truncate">{item.generic_name || "Technical Formula Verified"}</p>
          )}
        </div>
        
        <div className="mt-auto pt-6 flex items-end justify-between border-t border-white/5">
          <div className="space-y-1">
            <p className="text-[9px] font-black text-gray-500 uppercase tracking-widest leading-none">Unit Price</p>
            {isGuest ? (
              <div className="flex items-center gap-2 text-emerald-500 font-black">
                <Icons.Lock className="w-3.5 h-3.5" />
                <span className="text-[11px] uppercase tracking-widest">Private</span>
              </div>
            ) : (
              <div className="flex items-baseline gap-1">
                <span className="text-2xl font-black text-white">{(item.sell_price || item.price || 0).toLocaleString()}</span>
                <span className="text-[11px] text-emerald-500 font-black uppercase">MMK</span>
              </div>
            )}
          </div>

          <button
            onClick={(e) => {
              e.stopPropagation();
              onAddToCart(item);
            }}
            className="w-14 h-14 bg-white/5 hover:bg-emerald-500 border border-white/10 hover:border-emerald-400 text-gray-400 hover:text-[#0d1117] rounded-[1.25rem] flex items-center justify-center transition-all group/btn active:scale-90 shadow-xl"
          >
            {isGuest ? (
              <Icons.LogIn className="w-6 h-6 group-hover/btn:translate-x-0.5 transition-transform" />
            ) : (
              <Icons.Plus className="w-7 h-7 group-hover/btn:rotate-90 transition-transform duration-300" strokeWidth={3} />
            )}
          </button>
        </div>
      </div>
    </div>
  );
}

function getCategoryIcon(name: string, className = "w-4 h-4") {
  const n = name.toLowerCase();
  if (n.includes("pain") || n.includes("relie")) return <Icons.Activity className={className} />;
  if (n.includes("flu") || n.includes("cold")) return <Icons.Thermometer className={className} />;
  if (n.includes("anti")) return <Icons.FlaskConical className={className} />;
  if (n.includes("vitam")) return <Icons.Zap className={className} />;
  if (n.includes("heart") || n.includes("cardio")) return <Icons.Heart className={className} />;
  if (n.includes("skin") || n.includes("prepar")) return <Icons.Sparkles className={className} />;
  if (n.includes("gastro")) return <Icons.Stethoscope className={className} />;
  if (n.includes("allergy")) return <Icons.Bean className={className} />;
  if (n.includes("resp")) return <Icons.Wind className={className} />;
  return <Icons.ShieldPlus className={className} strokeWidth={2} />;
}