"use client";

import { useEffect, useState } from "react";
import { medicineService } from "@/services/medicineService";
import { categoryService } from "@/services/categoryService";
import ProductDetailModal from "@/components/ProductDetailModal";
import { useCart } from "@/context/CartContext";
import toast from "react-hot-toast";
import { getImageUrl } from "@/utils/imageHelper";
import * as Icons from "lucide-react";

interface Props {
  user: any;
}

export default function RegisteredHomePage({ user }: Props) {
  const { addToCart: addToCartContext } = useCart();
  const [medicines, setMedicines] = useState([]);
  const [filteredMedicines, setFilteredMedicines] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeCategory, setActiveCategory] = useState("All");
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedMedicine, setSelectedMedicine] = useState<any>(null);

  const displayName = user?.name || user?.email?.split('@')[0] || "Member";

  const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return "Good Morning";
    if (hour < 18) return "Good Afternoon";
    return "Good Evening";
  };

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

  /**
   * Main filtering logic for both Search and Category
   */
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
    addToCartContext(item, 1);
    toast.success(`${item.name} added to cart!`, { icon: '🛒' });
  };

  if (loading) return (
    <div className="min-h-screen bg-[#0d1117] flex items-center justify-center">
      <div className="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
    </div>
  );

  return (
    <main className="min-h-screen bg-[#0d1117] text-white pb-20 font-sans">
      <div className="container mx-auto px-6 flex flex-col lg:flex-row gap-12 pt-12">
        
        {/* Persistent Side Navigation */}
        <NavigationSidebar 
          categories={categories} 
          activeCategory={activeCategory} 
          setActiveCategory={setActiveCategory} 
          medicines={medicines}
        />

        {/* Main Product Area */}
        <div className="flex-1 space-y-12">
           {/* Top Header Section */}
           <div className="flex flex-col md:flex-row md:items-center justify-between gap-6">
              <div className="flex-1">
                <h1 className="text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                  {getGreeting()}, <span className="text-emerald-500">{displayName}</span>
                </h1>
                <p className="text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mt-2">Personal Pharmacy Terminal</p>
              </div>

              <div className="w-full md:max-w-md relative">
                <div className="relative flex items-center bg-[#161b22] border border-white/5 rounded-[1.25rem] overflow-hidden shadow-2xl group focus-within:border-emerald-500/50 transition-all h-14">
                  <div className="pl-5 text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                    <Icons.Search className="w-5 h-5" />
                  </div>
                  <input
                    type="text"
                    placeholder='Search Medicine (e.g. Paracetamol)...'
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                    className="w-full bg-transparent py-4 px-4 text-[11px] font-bold text-white outline-none placeholder:text-gray-700"
                  />
                </div>
              </div>
           </div>

           {/* Mobile Department Selector (Only visible on small screens) */}
           <div className="lg:hidden">
              <select 
                value={activeCategory}
                onChange={(e) => setActiveCategory(e.target.value)}
                className="w-full h-14 bg-[#161b22] border border-white/5 rounded-2xl px-6 text-[11px] font-black uppercase tracking-widest text-emerald-500 focus:outline-none"
              >
                <option value="All">All Categories</option>
                {categories.map((cat: any) => (
                  <option key={cat.id} value={cat.name}>{cat.name}</option>
                ))}
              </select>
           </div>

           {/* Product Content - Streamlined Unified Grid */}
           <div className="space-y-12 pb-20">
              <div className="flex items-center justify-between">
                <div className="flex items-center gap-4">
                  <div className="w-1.5 h-8 bg-emerald-500 rounded-full"></div>
                  <div>
                    <h2 className="text-xl font-black uppercase tracking-widest text-white">
                      {searchTerm ? "Search Results" : (activeCategory === "All" ? "All Products" : activeCategory)}
                    </h2>
                    <p className="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                      Showing {filteredMedicines.length} medical items
                    </p>
                  </div>
                </div>
                
                {(searchTerm || activeCategory !== "All") && (
                  <button 
                    onClick={() => { setSearchTerm(""); setActiveCategory("All"); }}
                    className="px-4 py-2 bg-white/5 hover:bg-white/10 border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-emerald-500 hover:text-white transition-all flex items-center gap-2 shadow-xl"
                  >
                    <Icons.X className="w-3 h-3" />
                    Clear Filters
                  </button>
                )}
              </div>

              {filteredMedicines.length > 0 ? (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                  {filteredMedicines.map((item: any) => (
                    <ProductCard key={item.id} item={item} onSelect={setSelectedMedicine} onAddToCart={addToCart} />
                  ))}
                </div>
              ) : (
                <div className="bg-[#161b22] border border-white/5 rounded-[3rem] p-24 text-center shadow-2xl">
                  <div className="w-20 h-20 bg-white/5 rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/5">
                    <Icons.SearchX className="w-10 h-10 text-gray-600" />
                  </div>
                  <h3 className="text-2xl font-black text-white mb-3 uppercase tracking-widest">No Matches Found</h3>
                  <p className="text-gray-500 text-sm font-bold max-w-xs mx-auto leading-relaxed">
                    We couldn't find any medicines matching your current filters. Try a different search or department.
                  </p>
                </div>
              )}
           </div>
        </div>
      </div>

      {selectedMedicine && (
        <ProductDetailModal
          item={selectedMedicine}
          onClose={() => setSelectedMedicine(null)}
          onAddToCart={addToCart}
        />
      )}
    </main>
  );
}

/**
 * Professional Dashboard Sidebar Navigation
 */
function NavigationSidebar({ categories, activeCategory, setActiveCategory, medicines }: any) {
  return (
    <aside className="w-72 flex-shrink-0 hidden lg:block scroll-mt-20">
      <div className="sticky top-12 space-y-8">
        <div>
          <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-500/50 mb-6 px-4">Departments</h3>
          <div className="space-y-1">
            <button 
              onClick={() => setActiveCategory("All")}
              className={`w-full flex items-center justify-between px-4 py-3.5 rounded-2xl transition-all duration-300 group
                ${activeCategory === "All" 
                  ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 shadow-lg shadow-emerald-500/5' 
                  : 'text-gray-400 hover:text-white hover:bg-white/5'}`}
            >
              <div className="flex items-center gap-4">
                <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-colors
                  ${activeCategory === "All" ? 'bg-emerald-500 text-[#0d1117]' : 'bg-white/5 text-gray-500 group-hover:text-emerald-500 group-hover:bg-emerald-500/10'}`}>
                  <Icons.LayoutGrid className="w-5 h-5" />
                </div>
                <span className="text-[11px] font-black uppercase tracking-widest">All Products</span>
              </div>
              <span className="text-[10px] font-bold opacity-40">{medicines.length}</span>
            </button>

            {categories.map((cat: any) => {
              const count = medicines.filter((m: any) => m.category_id === cat.id).length;
              const isActive = activeCategory === cat.name;
              
              return (
                <button 
                  key={cat.id} 
                  onClick={() => setActiveCategory(cat.name)}
                  className={`w-full flex items-center justify-between px-4 py-3.5 rounded-2xl transition-all duration-300 group
                    ${isActive 
                      ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 shadow-lg shadow-emerald-500/5' 
                      : 'text-gray-400 hover:text-white hover:bg-white/5'}`}
                >
                  <div className="flex items-center gap-4">
                    <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-colors
                      ${isActive ? 'bg-emerald-500 text-[#0d1117]' : 'bg-white/5 text-gray-500 group-hover:text-emerald-500 group-hover:bg-emerald-500/10'}`}>
                      {cat.name.toLowerCase().includes("pain") && <Icons.Activity className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("cold") && <Icons.Thermometer className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("anti") && <Icons.FlaskConical className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("respir") && <Icons.Wind className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("vitam") && <Icons.Zap className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("cardio") && <Icons.Heart className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("allergy") && <Icons.Flower2 className="w-5 h-5" />}
                      {cat.name.toLowerCase().includes("skin") && <Icons.Sparkles className="w-5 h-5" />}
                      {!["pain","cold","anti","respir","vitam","cardio","allergy","skin"].some(k => cat.name.toLowerCase().includes(k)) && <Icons.Package className="w-5 h-5" />}
                    </div>
                    <span className="text-[11px] font-black uppercase tracking-widest text-left">{cat.name}</span>
                  </div>
                  <span className="text-[10px] font-bold opacity-40">{count}</span>
                </button>
              );
            })}
          </div>
        </div>

        {/* Promotion or Support Card */}
        <div className="bg-gradient-to-br from-emerald-500/10 to-emerald-900/10 border border-white/5 rounded-[2rem] p-6 relative overflow-hidden group">
           <div className="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 blur-[40px] rounded-full group-hover:bg-emerald-500/20 transition-all"></div>
           <Icons.ShieldCheck className="w-8 h-8 text-emerald-500 mb-4" />
           <h4 className="text-white text-xs font-black uppercase tracking-widest mb-2">Verified Care</h4>
           <p className="text-gray-500 text-[10px] font-bold leading-relaxed mb-4">Licensed pharmacists are available 24/7 for consultation.</p>
           <button className="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] flex items-center gap-2 group-hover:gap-3 transition-all">
             Contact Support <Icons.ArrowRight className="w-3 h-3" />
           </button>
        </div>
      </div>
    </aside>
  );
}

/**
 * Sub-component for individual Product Cards
 */
function ProductCard({ item, onSelect, onAddToCart }: any) {
  return (
    <div
      onClick={() => onSelect(item)}
      className="group bg-[#161b22] border border-white/5 rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all duration-500 flex flex-col shadow-2xl cursor-pointer relative"
    >
      <div className="relative h-48 bg-white flex items-center justify-center p-10 overflow-hidden">
        <span className={`absolute top-4 right-4 text-[9px] font-black px-2.5 py-1 rounded-full z-10 tracking-widest ${item.stock_quantity > 0 ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white'}`}>
          {item.stock_quantity > 0 ? 'AVAILABLE' : 'SOLD OUT'}
        </span>

        {item.image ? (
          <img
            src={getImageUrl(item.image)}
            width="100"
            height="100"
            className="max-h-full object-contain group-hover:scale-110 transition-transform duration-700 ease-out"
            alt={item.name}
          />
        ) : (
          <div className="text-emerald-500/30 group-hover:text-emerald-500 transition-colors">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2" strokeLinecap="round" strokeLinejoin="round">
              <path d="m10.5 20.5 9.5-9.5a4.5 4.5 0 0 0-7.74-7.76l-9.5 9.5a4.5 4.5 0 0 0 7.74 7.76Z" />
              <path d="m8.5 8.5 7 7" />
            </svg>
          </div>
        )}
      </div>

      <div className="p-6 flex flex-col flex-1">
        <p className="text-[12px] font-black text-emerald-500/60 mb-1 tracking-wide">{item.category?.name || "General"}</p>
        <h4 className="text-white font-bold text-lg mb-4 line-clamp-1 group-hover:text-emerald-500 transition-colors tracking-wide">{item.name}</h4>

        <div className="mt-auto pt-4 border-t border-white/5 flex items-center justify-between">
          <div className="flex flex-col">
            <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Price</span>
            <div className="flex items-baseline gap-1">
              <span className="text-lg font-black text-white">{Math.floor(item.sell_price || 0).toLocaleString()}</span>
              <span className="text-[11px] text-emerald-500 font-black">MMK</span>
            </div>
          </div>
          <button
            onClick={(e) => {
              e.stopPropagation();
              onAddToCart(item);
            }}
            disabled={item.stock_quantity <= 0}
            className="w-10 h-10 bg-emerald-600 hover:bg-emerald-400 disabled:bg-gray-800 disabled:text-gray-700 text-[#0d1117] rounded-xl transition-all active:scale-90 shadow-lg flex items-center justify-center"
          >
            <Icons.Plus className="w-5 h-5" strokeWidth={3} />
          </button>
        </div>
      </div>
    </div>
  );
}