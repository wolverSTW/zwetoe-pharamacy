"use client";

import { startTransition, useDeferredValue, useEffect, useState } from "react";
import toast from "react-hot-toast";
import * as Icons from "lucide-react";
import ProductDetailModal from "@/components/ui/ProductDetailModal";
import ProductImage from "@/components/ui/ProductImage";
import { useCart } from "@/context/CartContext";
import { categoryService } from "@/services/categoryService";
import { medicineService } from "@/services/medicineService";
import { Category, Medicine, User } from "@/types/store";
import { getImageUrl } from "@/utils/imageHelper";

interface Props {
  user: User;
}

export default function RegisteredHomePage({ user }: Props) {
  const { addToCart: addToCartContext } = useCart();
  const [medicines, setMedicines] = useState<Medicine[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeCategory, setActiveCategory] = useState("All");
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedMedicine, setSelectedMedicine] = useState<Medicine | null>(null);
  const deferredSearchTerm = useDeferredValue(searchTerm.trim().toLowerCase());

  const displayName = user.name || user.email?.split("@")[0] || "Member";

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
          categoryService.getAll(),
        ]);

        startTransition(() => {
          setMedicines(medData);
          setCategories(catData);
        });
      } catch (error: unknown) {
        const message = error instanceof Error ? error.message : String(error);
        console.error("Home Data Fetch Failure:", message);
      } finally {
        setLoading(false);
      }
    };

    fetchData();
  }, []);

  const filteredMedicines = medicines.filter((medicine) => {
    if (activeCategory !== "All" && medicine.category?.name !== activeCategory) {
      return false;
    }

    if (!deferredSearchTerm) {
      return true;
    }

    return (
      medicine.name.toLowerCase().includes(deferredSearchTerm) ||
      medicine.generic_name?.toLowerCase().includes(deferredSearchTerm)
    );
  });

  const addToCart = (item: Medicine) => {
    addToCartContext(item, 1);
    toast.success(`${item.name} added!`, {
      style: { background: "#10b981", color: "#fff", borderRadius: "12px", fontSize: "12px" },
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#0d1117] flex items-center justify-center">
        <div className="w-8 h-8 border-2 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
      </div>
    );
  }

  return (
    <main className="min-h-screen bg-[#0d1117] text-white pb-20 font-sans selection:bg-emerald-500/20">
      <section className="pt-5 pb-3 border-b border-white/5">
        <div className="container mx-auto px-6">
          <div className="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div className="space-y-2">
              <h1 className="text-xl md:text-2xl font-black text-white tracking-tight">
                {getGreeting()}, <span className="text-emerald-500">{displayName}</span>
              </h1>
            </div>
            <div className="w-full md:max-w-md relative group">
              <div className="absolute inset-y-0 left-4 flex items-center text-gray-500 group-focus-within:text-emerald-500 transition-colors">
                <Icons.Search className="w-5 h-5" strokeWidth={2.5} />
              </div>
              <input
                type="text"
                placeholder="Search....."
                value={searchTerm}
                onChange={(event) => setSearchTerm(event.target.value)}
                className="w-full bg-[#161b22] border border-white/5 rounded-2xl py-2 pl-12 pr-6 text-sm font-bold text-white focus:outline-none focus:border-emerald-500/50 transition-all shadow-2xl"
              />
            </div>
          </div>
        </div>
      </section>

      <section className="container mx-auto px-2 pt-16">
        <div className="flex items-center justify-between mb-12">
          <div className="flex items-center gap-4">
            <div className="relative group">
              <button
                onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                className="flex items-center gap-4 px-6 py-3 bg-[#161b22] border border-white/10 rounded-2xl text-[10px] font-black uppercase tracking-widest text-emerald-500 hover:border-emerald-500/50 transition-all shadow-xl min-w-[240px] justify-between"
              >
                <div className="flex items-center gap-3">
                  <div className="text-emerald-500/80">
                    {activeCategory === "All" ? (
                      <Icons.LayoutGrid className="w-4 h-4" />
                    ) : (
                      getCategoryIcon(activeCategory)
                    )}
                  </div>
                  <span>{activeCategory === "All" ? "All Categories" : activeCategory}</span>
                </div>
                <Icons.ChevronDown
                  className={`w-4 h-4 transition-transform duration-300 ${isDropdownOpen ? "rotate-180" : ""}`}
                />
              </button>
              {isDropdownOpen && (
                <>
                  <div className="fixed inset-0 z-40" onClick={() => setIsDropdownOpen(false)}></div>
                  <div className="absolute top-full left-0 right-0 mt-3 z-50 bg-[#1c2128] border border-white/10 rounded-2xl overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-3xl">
                    <button
                      onClick={() => {
                        setActiveCategory("All");
                        setIsDropdownOpen(false);
                      }}
                      className={`w-full text-left px-6 py-4 text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3 border-b border-white/5 ${
                        activeCategory === "All" ? "bg-emerald-500 text-[#0d1117]" : "hover:bg-white/5 text-gray-400"
                      }`}
                    >
                      <Icons.LayoutGrid className="w-4 h-4" />
                      All Categories
                    </button>
                    <div className="max-h-[300px] overflow-y-auto no-scrollbar">
                      {categories.map((category) => (
                        <button
                          key={category.id}
                          onClick={() => {
                            setActiveCategory(category.name);
                            setIsDropdownOpen(false);
                          }}
                          className={`w-full text-left px-3 py-3 text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3 border-b border-white/5 last:border-0 ${
                            activeCategory === category.name
                              ? "bg-emerald-500 text-[#0d1117]"
                              : "hover:bg-white/5 text-gray-400"
                          }`}
                        >
                          <div
                            className={
                              activeCategory === category.name ? "text-[#0d1117]" : "text-emerald-500/50"
                            }
                          >
                            {getCategoryIcon(category.name)}
                          </div>
                          {category.name}
                        </button>
                      ))}
                    </div>
                  </div>
                </>
              )}
            </div>
            {(searchTerm || activeCategory !== "All") && (
              <button
                onClick={() => {
                  setSearchTerm("");
                  setActiveCategory("All");
                }}
                className="w-12 h-12 flex items-center justify-center bg-[#161b22] border border-white/10 rounded-2xl text-emerald-500 hover:text-white hover:bg-emerald-500 transition-all shadow-xl group"
                title="Reset Filters"
              >
                <Icons.RotateCcw
                  className="w-4 h-4 group-hover:rotate-180 transition-transform duration-500"
                  strokeWidth={3}
                />
              </button>
            )}
          </div>
        </div>

        {filteredMedicines.length > 0 ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-8">
            {filteredMedicines.map((item) => (
              <ProductCard key={item.id} item={item} onSelect={setSelectedMedicine} onAddToCart={addToCart} />
            ))}
          </div>
        ) : (
          <div className="bg-[#161b22] border border-white/5 rounded-[3rem] p-32 text-center shadow-2xl">
            <Icons.DatabaseBackup className="w-16 h-16 text-gray-700 mx-auto mb-8" strokeWidth={1} />
            <h3 className="text-2xl font-black text-white mb-2 uppercase tracking-widest">Entry Not Found</h3>
            <p className="text-gray-500 text-sm font-bold max-w-xs mx-auto">
              Try refining your search or department filter.
            </p>
          </div>
        )}
      </section>

      {selectedMedicine && (
        <ProductDetailModal item={selectedMedicine} onClose={() => setSelectedMedicine(null)} />
      )}
    </main>
  );
}

interface ProductCardProps {
  item: Medicine;
  onSelect: (item: Medicine) => void;
  onAddToCart: (item: Medicine) => void;
}

function ProductCard({ item, onSelect, onAddToCart }: ProductCardProps) {
  return (
    <div
      onClick={() => onSelect(item)}
      className="group bg-[#161b22] border border-white/5 rounded-3xl overflow-hidden hover:border-emerald-500/30 transition-all duration-500 cursor-pointer flex flex-col shadow-2xl"
    >
      <div className="relative h-48 bg-white flex items-center justify-center p-5 overflow-hidden">
        <span
          className={`absolute top-4 right-4 text-[9px] font-black px-3 py-1.5 rounded-full z-10 tracking-widest uppercase ${
            item.stock_quantity > 0 ? "bg-emerald-500 text-white" : "bg-red-500 text-white"
          }`}
        >
          {item.stock_quantity > 0 ? "In Stock" : "Out of Stock"}
        </span>
        <ProductImage
          src={getImageUrl(item.image)}
          alt={item.name}
          sizes="(min-width: 1536px) 20vw, (min-width: 1280px) 25vw, (min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
          className="object-contain group-hover:scale-110 transition-transform duration-1000 ease-out"
          iconClassName="w-12 h-12 text-gray-100"
        />
      </div>
      <div className="p-6 flex flex-col flex-1">
        <h4 className="text-lg font-black text-white mb-1 tracking-tight truncate group-hover:text-emerald-500 transition-colors uppercase">
          {item.name}
        </h4>
        <div className="mt-auto flex items-center justify-between gap-4 pt-4 border-t border-white/5">
          <div className="flex flex-col">
            <span className="text-[9px] font-black text-gray-600 uppercase tracking-widest leading-none mb-1">
              Price
            </span>
            <div className="flex items-baseline gap-1">
              <span className="text-xl font-black text-white">{Math.floor(item.sell_price || 0).toLocaleString()}</span>
              <span className="text-[11px] text-emerald-500 font-black">MMK</span>
            </div>
          </div>
          <button
            onClick={(event) => {
              event.stopPropagation();
              onAddToCart(item);
            }}
            disabled={item.stock_quantity <= 0}
            className="w-11 h-11 bg-emerald-600 hover:bg-emerald-400 disabled:bg-gray-800 disabled:text-gray-700 text-[#0d1117] rounded-2xl transition-all active:scale-90 shadow-xl flex items-center justify-center group/btn"
          >
            <Icons.Plus className="w-6 h-6 group-hover/btn:rotate-90 transition-transform duration-300" strokeWidth={3} />
          </button>
        </div>
      </div>
    </div>
  );
}

function getCategoryIcon(name: string) {
  const normalizedName = name.toLowerCase();
  if (normalizedName.includes("pain")) return <Icons.Activity className="w-4 h-4" />;
  if (normalizedName.includes("flu") || normalizedName.includes("cold")) return <Icons.Thermometer className="w-4 h-4" />;
  if (normalizedName.includes("anti")) return <Icons.FlaskConical className="w-4 h-4" />;
  if (normalizedName.includes("vitam")) return <Icons.Zap className="w-4 h-4" />;
  if (normalizedName.includes("heart") || normalizedName.includes("cardio")) return <Icons.Heart className="w-4 h-4" />;
  if (normalizedName.includes("skin")) return <Icons.Sparkles className="w-4 h-4" />;
  return <Icons.ShieldPlus className="w-4 h-4" strokeWidth={2} />;
}
