"use client";

import { useEffect, useState } from "react";
import { medicineService } from "@/services/medicineService";
import { categoryService } from "@/services/categoryService";
import ProductDetailModal from "@/components/ProductDetailModal";
import { useCart } from "@/context/CartContext";
import toast from "react-hot-toast";
import { getImageUrl } from "@/utils/imageHelper";

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
      <section className="pt-12 pb-6">
        <div className="container mx-auto px-6">
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div className="flex-1">
              <h1 className="text-3xl md:text-4xl font-black text-white tracking-tight leading-tight">
                {getGreeting()}, <span className="text-emerald-500">{displayName.split(' ')[0]}!</span>
              </h1>
            </div>

            <div className="w-full lg:max-w-md">
              <div className="relative flex items-center bg-[#161b22] border border-white/5 rounded-2xl overflow-hidden shadow-2xl group focus-within:border-emerald-500/50 transition-all">
                <div className="pl-5 text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <input
                  type="text"
                  placeholder='Search Medicines...'
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                  className="w-full bg-transparent py-4 px-4 text-xs font-bold text-white outline-none"
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
            className={`px-8 py-3 rounded-xl text-[12px] font-black tracking-wide transition-all
              ${activeCategory === "All" ? 'bg-emerald-500 text-white' : 'bg-[#161b22] text-gray-400 border border-white/5'}`}
          >
            All
          </button>
          {categories.map((cat: any) => (
            <button 
              key={cat.id} 
              onClick={() => setActiveCategory(cat.name)} 
              className={`px-8 py-3 rounded-xl text-[12px] font-black transition-all
                ${activeCategory === cat.name ? 'bg-emerald-500 text-white' : 'bg-[#161b22] text-gray-400 border border-white/5'}`}
            >
              {cat.name}
            </button>
          ))}
        </div>
      </section>

      {/* Product Grid  */}
      <section className="container mx-auto px-6">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6">
          {filteredMedicines.map((item: any) => (
            <div 
              key={item.id} 
              onClick={() => setSelectedMedicine(item)}
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
                      <span className="text-lg font-black text-white">{Math.floor(item.price).toLocaleString()}</span>
                      <span className="text-[11px] text-emerald-500 font-black">MMK</span>
                    </div>
                  </div>
                  <button 
                    onClick={(e) => {
                      e.stopPropagation();
                      addToCart(item);
                    }}
                    disabled={item.stock_quantity <= 0}
                    className="w-10 h-10 bg-emerald-600 hover:bg-emerald-400 disabled:bg-gray-800 disabled:text-gray-700 text-[#0d1117] rounded-xl transition-all active:scale-90 shadow-lg flex items-center justify-center"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                      <path d="M12 5v14M5 12h14" />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </section>

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