"use client";

import { useEffect, useState } from "react";
import { medicineService } from "@/services/medicineService";
import { categoryService } from "@/services/categoryService";
import ProductDetailModal from "@/components/ProductDetailModal";
import toast from "react-hot-toast";

interface Props {
  user: any;
}

export default function MedicinesPage({ user }: Props) {
  const [medicines, setMedicines] = useState([]);
  const [filteredMedicines, setFilteredMedicines] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeCategory, setActiveCategory] = useState("All");
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedMedicine, setSelectedMedicine] = useState<any>(null);

  const isGuest = !user;

  useEffect(() => {
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
        console.error("Error fetching data:", error);
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
        m.name.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }
    setFilteredMedicines(result);
  }, [activeCategory, searchTerm, medicines]);

  const addToCart = (item: any) => {
    if (isGuest) {
      toast.error("Authentication required to purchase", {
        style: {
          borderRadius: '12px',
          background: '#161b22',
          color: '#fff',
          border: '1px solid rgba(255,255,255,0.1)',
          fontSize: '12px',
          fontWeight: 'bold'
        }
      });
      return;
    }
    toast.success(`${item.name} added to cart`, {
      style: {
        borderRadius: '12px',
        background: '#161b22',
        color: '#fff',
        border: '1px solid rgba(16,185,129,0.2)',
        fontSize: '12px',
        fontWeight: 'bold'
      }
    });
  };

  if (loading) return (
    <div className="min-h-screen bg-[#0d1117] flex items-center justify-center">
      <div className="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
    </div>
  );

  return (
    <main className="min-h-screen bg-[#0d1117] text-white pb-20 font-sans">
      {/* Header Section */}
      <section className="pt-12 pb-6">
        <div className="container mx-auto px-6">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
              <h1 className="text-3xl md:text-4xl font-black text-white tracking-tight uppercase">
                Explore <span className="text-emerald-500">Medicines</span>
              </h1>
              {isGuest && (
                <div className="flex items-center gap-2 mt-2 text-gray-500">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  <p className="text-[10px] font-bold uppercase tracking-widest">Pricing locked for guests</p>
                </div>
              )}
            </div>

            <div className="w-full lg:max-w-md">
              <div className="relative flex items-center bg-[#161b22] border border-white/5 rounded-2xl group focus-within:border-emerald-500/50 transition-all">
                <div className="pl-5 text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input
                  type="text"
                  placeholder='SEARCH CATALOGUE...'
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full bg-transparent py-4 px-4 text-[11px] font-bold text-white outline-none uppercase tracking-wider"
                />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Categories Tabs */}
      <section className="container mx-auto px-6 py-8">
        <div className="flex overflow-x-auto gap-3 no-scrollbar pb-2">
          <button 
            onClick={() => setActiveCategory("All")} 
            className={`px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
              ${activeCategory === "All" ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-[#161b22] text-gray-400 border border-white/5'}`}
          >
            All Items
          </button>
          {categories.map((cat: any) => (
            <button 
              key={cat.id} 
              onClick={() => setActiveCategory(cat.name)} 
              className={`px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                ${activeCategory === cat.name ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-[#161b22] text-gray-400 border border-white/5'}`}
            >
              {cat.name}
            </button>
          ))}
        </div>
      </section>

      {/* Product Grid */}
      <section className="container mx-auto px-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
          {filteredMedicines.map((item: any) => (
            <div 
              key={item.id} 
              onClick={() => setSelectedMedicine(item)}
              className="group bg-[#161b22] border border-white/5 rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all duration-500 flex flex-col shadow-2xl cursor-pointer"
            >
              {/* Image Container */}
              <div className="relative h-48 bg-white flex items-center justify-center p-10 overflow-hidden">
                <div className={`absolute top-4 right-4 text-[9px] font-black px-2.5 py-1 rounded-full z-10 tracking-widest flex items-center gap-1.5 ${item.stock_quantity > 0 ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-red-500/10 text-red-600 border border-red-500/20'}`}>
                  <div className={`w-1.5 h-1.5 rounded-full ${item.stock_quantity > 0 ? 'bg-emerald-500' : 'bg-red-500'}`}></div>
                  {item.stock_quantity > 0 ? 'IN STOCK' : 'OUT OF STOCK'}
                </div>
                
                {item.image_url ? (
                  <img src={item.image_url} className="max-h-full object-contain group-hover:scale-110 transition-transform duration-700" alt={item.name} />
                ) : (
                  /* 🔥 Pill SVG Icon (Fallback) */
                  <div className="text-emerald-500/20 group-hover:text-emerald-500 transition-colors duration-500 transform group-hover:rotate-12">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round">
                      <path d="m10.5 20.5 9.5-9.5a4.5 4.5 0 0 0-7.74-7.76l-9.5 9.5a4.5 4.5 0 0 0 7.74 7.76Z" />
                      <path d="m8.5 8.5 7 7" />
                    </svg>
                  </div>
                )}
              </div>

              {/* Info Section */}
              <div className="p-6 flex flex-col flex-1">
                <p className="text-[10px] font-black text-emerald-500/60 mb-1 uppercase tracking-[0.15em]">{item.category?.name || "General"}</p>
                <h4 className="text-white font-bold text-sm mb-4 line-clamp-1 uppercase tracking-tight group-hover:text-emerald-500 transition-colors">{item.name}</h4>
                
                <div className="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
                  <div className="flex flex-col">
                    <span className="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Unit Price</span>
                    {isGuest ? (
                      <div className="flex items-center gap-1.5 mt-1 text-emerald-500/80">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span className="text-[10px] font-black uppercase tracking-tighter">Private</span>
                      </div>
                    ) : (
                      <div className="flex items-baseline gap-1 mt-0.5">
                        <span className="text-lg font-black text-white">{Math.floor(item.price).toLocaleString()}</span>
                        <span className="text-[10px] text-emerald-500 font-black">MMK</span>
                      </div>
                    )}
                  </div>
                  
                  <button 
                    onClick={(e) => { e.stopPropagation(); addToCart(item); }}
                    className={`w-10 h-10 rounded-xl transition-all active:scale-95 flex items-center justify-center
                      ${isGuest ? 'bg-white/5 text-gray-500' : 'bg-emerald-600 hover:bg-emerald-500 text-[#0d1117] shadow-lg shadow-emerald-900/20'}
                    `}
                  >
                    {isGuest ? (
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"/></svg>
                    ) : (
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    )}
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Modal Integration */}
      {selectedMedicine && (
        <ProductDetailModal 
          item={selectedMedicine} 
          onClose={() => setSelectedMedicine(null)}
          onAddToCart={addToCart}
          isGuest={isGuest}
        />
      )}
    </main>
  );
}