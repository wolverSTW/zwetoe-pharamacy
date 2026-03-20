"use client";

import { useState, useEffect } from "react";
import { useCart } from "@/context/CartContext";
import axios from "axios";
import { useRouter } from "next/navigation";
import Link from "next/link";

// --- SVG Icons ---
const StoreIcon = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>;
const TruckIcon = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>;
const MapPinIcon = () => <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>;
const BackIcon = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"><path d="m15 18-6-6 6-6"/></svg>;
const PillIcon = () => <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="opacity-30"><path d="m10.5 3.5 7 7-7 7-7-7a4.95 4.95 0 1 1 7-7Z"/><path d="m8.5 8.5 7 7"/></svg>;
const CheckIcon = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><polyline points="20 6 9 17 4 12"/></svg>;
const UploadIcon = () => <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>;
const SuccessIcon = () => <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#10b981" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>;
const AlertCircle = () => <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>;

export default function CartPage() {
  const { cart, removeFromCart, totalAmount, clearCart } = useCart();
  const router = useRouter();

  const [shippingMethod, setShippingMethod] = useState("pick-up");
  const [addressType, setAddressType] = useState("new");
  const [savedAddress, setSavedAddress] = useState<any>(null);
  const [finalTotal, setFinalTotal] = useState(0);
  const [orderId, setOrderId] = useState<string | null>(null);
  const [addressForm, setAddressForm] = useState({ house_number: "", street: "", town: "", township: "", region: "", phone: "" });

  const [showQRModal, setShowQRModal] = useState(false);
  const [modalView, setModalView] = useState<"payment" | "invoice" | "success">("payment");
  const [qrImage, setQrImage] = useState("");
  const [loading, setLoading] = useState(false);
  const [purchasedItems, setPurchasedItems] = useState<any[]>([]);
  const [isClient, setIsClient] = useState(false);
  const [screenshot, setScreenshot] = useState<File | null>(null);
  const [showError, setShowError] = useState(false);

  useEffect(() => {
    setIsClient(true);
    const userData = localStorage.getItem("user");
    if (userData) {
      const user = JSON.parse(userData);
      if (user.address) { setSavedAddress(user.address); setAddressType("default"); }
    }
  }, []);


  const handleConfirmOrder = () => {
    if (!cart || cart.length === 0) return;
    const token = localStorage.getItem("token");
    if (!token) return router.push("/login");

    setFinalTotal(totalAmount);
    setPurchasedItems([...cart]);
    setQrImage("/images/qr/kbz-pay-qr.jpg"); 
    setModalView("payment");
    setShowQRModal(true);
  };

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files?.[0]) {
      setScreenshot(e.target.files[0]);
      setShowError(false);
    }
  };

  const handleFinishProcess = async () => {
    if (!screenshot && modalView === "payment") {
      setShowError(true);
      return;
    }

    setLoading(true);
    const token = localStorage.getItem("token");

    try {
      const formData = new FormData();
      formData.append("payment_screenshot", screenshot as File);
      formData.append("shipping_method", shippingMethod);
      formData.append("payment_method", "kbzpay");
      
      const itemData = cart.map((item: any) => ({
        medicine_id: item.id,
        quantity: item.quantity || 1
      }));
      formData.append("items", JSON.stringify(itemData));

      if (shippingMethod === "delivery") {
        const addr = addressType === "default" ? savedAddress : addressForm;
        formData.append("address", JSON.stringify(addr));
      }

      const response = await axios.post(`${process.env.NEXT_PUBLIC_API_URL}/orders`, formData, {
        headers: { 
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data' 
        }
      });

      if (response.status === 201) {
        setOrderId(response.data.order_id);
        setModalView("success");
        clearCart();
      }
    } catch (error: any) {
      alert(error.response?.data?.message || "Order failed.");
    } finally {
      setLoading(false);
    }
  };

  if (!isClient) return null;

  return (
    <main className="min-h-screen bg-[#05070a] text-[#e1e1e1] antialiased">
      <nav className="border-b border-[#1a1d23] bg-[#05070a]/90 backdrop-blur-xl sticky top-0 z-50">
        <div className="container mx-auto px-6 h-16 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2">
            <div className="w-8 h-8 bg-emerald-500 rounded-md flex items-center justify-center font-bold text-black text-xl">+</div>
            <span className="text-white font-bold text-lg">ZweToe <span className="text-emerald-500">Pharmacy</span></span>
          </Link>
          <Link href="/" className="text-sm font-medium text-gray-400 hover:text-white transition-all">Continue Shopping</Link>
        </div>
      </nav>

      <div className="container mx-auto px-6 py-12 max-w-6xl">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10">
          
          <div className="lg:col-span-7 space-y-10">
            <header><h1 className="text-3xl font-extrabold text-white tracking-tight">Checkout</h1></header>

            {/* --- Logistics Section --- */}
            <section className="space-y-6">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-gray-500">01 — Logistics</h3>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button onClick={() => setShippingMethod("pick-up")} className={`p-6 rounded-xl border-2 flex items-center gap-4 transition-all ${shippingMethod === 'pick-up' ? 'border-emerald-500 bg-emerald-500/5 text-emerald-400' : 'border-[#1a1d23] bg-[#0f1115] text-gray-400 hover:border-gray-700'}`}>
                  <StoreIcon /> <span className="font-bold text-sm">Store Pickup</span>
                </button>
                <button onClick={() => setShippingMethod("delivery")} className={`p-6 rounded-xl border-2 flex items-center gap-4 transition-all ${shippingMethod === 'delivery' ? 'border-emerald-500 bg-emerald-500/5 text-emerald-400' : 'border-[#1a1d23] bg-[#0f1115] text-gray-400 hover:border-gray-700'}`}>
                  <TruckIcon /> <span className="font-bold text-sm">Home Delivery</span>
                </button>
              </div>

              {shippingMethod === "pick-up" && (
                <div className="animate-in fade-in slide-in-from-top-4 duration-500 space-y-4">
                  <div className="bg-[#0f1115] p-5 rounded-2xl border border-[#1a1d23] flex items-start gap-4">
                    <div className="p-2 bg-emerald-500/10 rounded-lg text-emerald-500"><MapPinIcon /></div>
                    <div>
                      <h4 className="text-sm font-bold text-white">Our Shop Address</h4>
                      <p className="text-xs text-gray-400 mt-1 leading-relaxed">No. 51, Corner of Lanmadaw Road & Bo Yone Street, Padigon, Thegon Township, Bago West, Myanmar.</p>
                    </div>
                  </div>
                  <div className="w-full h-72 rounded-2xl overflow-hidden border border-[#1a1d23]">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d945.5226247845142!2d95.45813426949839!3d18.5699582701896!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c70d1370d6d0d5%3A0x3e2faf1662ed0c1d!2sZwe%20Toe%20Pharmacy!5e0!3m2!1sen!2smm!4v1771490601871!5m2!1sen!2smm" width="100%" height="100%" style={{ border: 0 }} allowFullScreen loading="lazy"></iframe>
                  </div>
                </div>
              )}

              {shippingMethod === "delivery" && (
                <div className="animate-in fade-in slide-in-from-top-4 duration-500 space-y-6">
                  <div className="bg-amber-500/10 border border-amber-500/20 p-4 rounded-xl flex items-center gap-3 text-amber-500">
                    <AlertCircle />
                    <p className="text-xs font-bold uppercase tracking-tight">⚠ Delivery fees must be paid by the customers!</p>
                  </div>

                  <div className="bg-[#0f1115] p-6 rounded-2xl border border-[#1a1d23] space-y-6">
                    {savedAddress && (
                      <div className="flex gap-4 mb-4">
                        <button onClick={() => setAddressType("default")} className={`flex-1 py-3 rounded-xl text-xs font-bold transition-all ${addressType === 'default' ? 'bg-emerald-500 text-black' : 'bg-[#1a1d23] text-gray-400'}`}>Use Saved</button>
                        <button onClick={() => setAddressType("new")} className={`flex-1 py-3 rounded-xl text-xs font-bold transition-all ${addressType === 'new' ? 'bg-emerald-500 text-black' : 'bg-[#1a1d23] text-gray-400'}`}>Add New</button>
                      </div>
                    )}
                    {addressType === "new" ? (
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {Object.keys(addressForm).map((field) => (
                          <div key={field} className="space-y-2">
                            <label className="text-[10px] uppercase font-bold text-gray-500 ml-1">{field.replace('_', ' ')}</label>
                            <input type="text" className="w-full bg-[#05070a] border border-[#1a1d23] rounded-xl px-4 py-3 text-sm focus:border-emerald-500 outline-none transition-all" value={(addressForm as any)[field]} onChange={(e) => setAddressForm({...addressForm, [field]: e.target.value})}/>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <div className="p-4 bg-[#05070a] rounded-xl border border-emerald-500/30">
                        <p className="text-xs text-emerald-500 font-bold mb-2 uppercase tracking-widest">Selected Address</p>
                        <p className="text-sm text-gray-300 leading-relaxed">{savedAddress.house_number}, {savedAddress.street}, {savedAddress.township}, {savedAddress.region}</p>
                        <p className="text-sm text-white font-bold mt-2">{savedAddress.phone}</p>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </section>

            {/* --- Selected Items Section --- */}
            <section className="space-y-4">
              <h3 className="text-xs font-semibold uppercase tracking-wider text-gray-500">02 — Selected Items</h3>
              <div className="space-y-3">
                {cart.map((item: any) => (
                  <div key={item.id} className="bg-[#0f1115] p-4 rounded-xl flex items-center justify-between border border-[#1a1d23]">
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 bg-[#05070a] rounded-lg flex items-center justify-center border border-[#1a1d23]">
                        {item.image_url ? <img src={item.image_url} className="w-full h-full object-cover" alt="" /> : <PillIcon />}
                      </div>
                      <div>
                        <h4 className="font-bold text-sm text-white">{item.name}</h4>
                        <p className="text-emerald-500 text-xs font-semibold">{(item.sell_price || item.price || 0).toLocaleString()} MMK x {item.quantity}</p>
                      </div>
                    </div>
                    <button onClick={() => removeFromCart(item.id)} className="text-gray-500 hover:text-red-500 text-xs font-bold px-3 py-1 border border-[#1a1d23] rounded-md transition-all">Remove</button>
                  </div>
                ))}
              </div>
            </section>
          </div>

          {/* --- Sidebar Summary --- */}
          <div className="lg:col-span-5">
            <div className="bg-[#0f1115] p-8 md:p-10 rounded-[2.5rem] border border-[#1a1d23] sticky top-24 shadow-2xl">
              <h3 className="text-xs font-bold uppercase tracking-[0.2em] mb-8 text-gray-500">Price Details</h3>
              <div className="space-y-4 mb-8 pt-6 border-t border-[#1a1d23]">
                <div className="flex justify-between items-center text-sm font-medium"><span className="text-gray-400">Subtotal</span><span className="text-white font-bold">{totalAmount.toLocaleString()} MMK</span></div>
                <div className="flex justify-between items-end border-t border-[#1a1d23] pt-6 mt-2">
                  <span className="text-gray-400 font-bold uppercase text-[10px] tracking-widest">Total Payable</span>
                  <div className="text-right"><span className="text-4xl font-black text-white leading-none">{totalAmount.toLocaleString()}</span><span className="text-xs font-bold text-gray-600 ml-2">MMK</span></div>
                </div>
              </div>
              <button onClick={handleConfirmOrder} disabled={loading || cart.length === 0} className="w-full mt-10 bg-emerald-500 hover:bg-emerald-400 py-4 rounded-xl font-bold text-black uppercase text-sm transition-all shadow-lg active:scale-95">
                {loading ? "Processing..." : "Confirm & Pay"}
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* --- QR/Invoice Modal --- */}
      {showQRModal && (
        <div className="fixed inset-0 bg-[#05070a]/95 backdrop-blur-md flex items-center justify-center z-60 p-4 animate-in fade-in duration-300">
          <div className="bg-[#0f1115] rounded-2xl border border-[#1a1d23] max-w-5xl w-full overflow-hidden shadow-2xl flex flex-col md:flex-row relative">
            {modalView !== "success" && (
              <button onClick={() => modalView === "invoice" ? setModalView("payment") : setShowQRModal(false)} className="absolute top-6 left-6 z-20 p-2 bg-[#1a1d23] hover:bg-white text-gray-400 hover:text-black rounded-full transition-all no-print"><BackIcon /></button>
            )}

            {modalView === "payment" ? (
              <>
                <div className="p-8 md:p-12 flex-1 flex flex-col justify-center space-y-8">
                  <header className="space-y-2">
                    <h2 className="text-3xl font-black text-white uppercase leading-tight pt-5">Pay & Upload<br/><span className="text-emerald-500">Receipt</span></h2>
                    <p className="text-xs text-gray-500 font-bold tracking-widest uppercase">Select Screenshot to finish</p>
                  </header>
                  <div className="bg-[#05070a] p-6 rounded-2xl border border-[#1a1d23]">
                    <p className="text-xs font-bold text-gray-500 uppercase mb-2">Grand Total</p>
                    <div className="flex items-baseline gap-2">
                      <span className="text-5xl font-black text-white">{finalTotal.toLocaleString()}</span>
                      <span className="text-lg font-bold text-emerald-500">MMK</span>
                    </div>
                  </div>
                  <div className="space-y-3">
                    <label className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Step 1: Upload Screenshot</label>
                    <label className={`flex items-center justify-center gap-3 w-full p-4 border-2 border-dashed rounded-xl cursor-pointer transition-all ${showError ? 'border-red-500 bg-red-500/5 text-red-500' : 'border-[#1a1d23] hover:bg-white/5 text-gray-400'}`}>
                      <UploadIcon />
                      <span className="text-xs font-bold uppercase">{screenshot ? screenshot.name : "Choose Photo"}</span>
                      <input type="file" className="hidden" onChange={handleFileChange} accept="image/*" />
                    </label>
                    <div className="min-h-20px">
                      {showError && (
                        <div className="flex items-center justify-center gap-2 bg-red-500/10 border border-red-500/20 py-2 px-4 rounded-lg animate-shake">
                          <AlertCircle />
                          <p className="text-[10px] text-red-500 font-bold uppercase tracking-wider">
                            Please upload a screenshot to confirm your order.
                          </p>
                        </div>
                      )}
                    </div>
                  </div>
                  <div className="grid grid-cols-1 gap-3">
                    <button onClick={() => setModalView("invoice")} className="text-xs font-bold text-gray-500 underline uppercase tracking-widest text-center mb-2">View Order Invoice</button>
                    <button onClick={handleFinishProcess} disabled={loading} className={`w-full py-4 rounded-xl font-bold text-sm flex items-center justify-center gap-3 transition-all ${screenshot ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-[#1a1d23] text-gray-600 cursor-not-allowed'}`}>
                       {loading ? "Confirming..." : <><CheckIcon /> Order Confirm</>}
                    </button>
                  </div>
                </div>
                <div className="bg-white p-10 md:p-16 flex flex-col items-center justify-center md:w-1/2">
                   <p className="text-xs font-bold text-blue-600 tracking-widest pb-4 uppercase">Step 2: Scan with KBZPay</p>
                   <div className="w-full max-w-70 aspect-square border-4 border-gray-100 p-2 rounded-xl">
                      <img src={qrImage || "/images/qr/kbz-pay-qr.jpg"} alt="QR" className="w-full h-full object-contain" />
                   </div>
                   <div className="mt-8 text-center text-black">
                      <p className="text-sm font-black tracking-widest uppercase">Zwe Toe Pharmacy</p>
                      <p className="text-[10px] font-bold text-gray-500 uppercase">A/C Name: Swunn Thut Wonn</p>
                   </div>
                </div>
              </>
            ) : modalView === "invoice" ? (
              <div className="flex-1 p-6 md:p-12 flex flex-col items-center justify-center bg-[#05070a]">
                <div className="max-w-md w-full bg-white text-black p-8 md:p-10 rounded-3xl shadow-2xl relative">
                  <div className="flex justify-between items-start border-b-2 border-dashed border-gray-200 pb-6 mb-6">
                    <div>
                      <h3 className="text-xl font-black uppercase">Invoice</h3>
                      <p className="text-[10px] font-bold text-gray-400 mt-1 uppercase">Pending Confirmation</p>
                    </div>
                    <div className="text-right">
                       <p className="text-lg font-black tracking-tight leading-none">ZweToe</p>
                       <p className="text-[10px] font-bold text-emerald-600 uppercase">Pharmacy</p>
                    </div>
                  </div>
                  <div className="space-y-3 mb-8 max-h-64 overflow-y-auto pr-2 custom-scroll">
                    {purchasedItems.map((item: any) => (
                      <div key={item.id} className="flex justify-between text-xs font-bold">
                        <span className="flex-1 pr-4">{item.name} <span className="text-gray-400 ml-1">x{item.quantity}</span></span>
                        <span>{((item.sell_price || item.price || 0) * (item.quantity || 1)).toLocaleString()}</span>
                      </div>
                    ))}
                  </div>
                  <div className="border-t-4 border-black pt-6 flex justify-between items-center font-black">
                    <span className="text-xs uppercase tracking-widest">Grand Total</span>
                    <span className="text-2xl">{finalTotal.toLocaleString()} MMK</span>
                  </div>
                </div>
                <button onClick={() => window.print()} className="mt-6 px-6 py-2 bg-white/10 hover:bg-white/20 text-white rounded-full text-xs font-bold uppercase transition-all no-print">Print Receipt</button>
              </div>
            ) : (
              <div className="flex-1 p-12 md:p-24 flex flex-col items-center justify-center text-center space-y-6 bg-[#0f1115]">
                <div className="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center animate-bounce"><SuccessIcon /></div>
                <div className="space-y-2">
                  <h2 className="text-4xl font-black text-white uppercase tracking-tight">Order Successful!</h2>
                  <p className="text-xs text-gray-500 font-bold tracking-widest uppercase mb-4">Order ID: #{orderId}</p>
                  <p className="text-gray-400 text-sm max-w-sm mx-auto leading-relaxed">Your order has been successfully placed. We will contact you via phone once we have verified your payment receipt.</p>
                </div>
                <button onClick={() => router.push("/")} className="bg-white text-black px-10 py-4 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all shadow-xl">Back to Home</button>
              </div>
            )}
          </div>
        </div>
      )}

      <style jsx>{`
        .custom-scroll::-webkit-scrollbar { width: 3px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
        @media print { .no-print { display: none !important; } body { background: white !important; } }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .animate-shake { animation: shake 0.2s ease-in-out 0s 2; }
      `}</style>
    </main>
  );
}