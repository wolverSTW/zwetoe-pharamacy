import React from 'react';
import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import '@testing-library/jest-dom';
import CartPage from '../app/cart/page';
import { useCart } from '../context/CartContext';
import { useRouter } from 'next/navigation';
import axios from 'axios';

// Mock dependencies
jest.mock('../context/CartContext', () => ({
  useCart: jest.fn(),
}));

jest.mock('next/navigation', () => ({
  useRouter: jest.fn(),
}));

jest.mock('axios');

describe('CartPage - Checkout Workflow', () => {
    const mockPush = jest.fn();
    const mockClearCart = jest.fn();
    const mockRemoveFromCart = jest.fn();
    
    const mockCart = [
        { id: 1, name: 'Paracetamol', sell_price: 500, quantity: 2, image_url: null },
    ];

    beforeEach(() => {
        (useRouter as jest.Mock).mockReturnValue({ push: mockPush });
        (useCart as jest.Mock).mockReturnValue({
            cart: mockCart,
            removeFromCart: mockRemoveFromCart,
            totalAmount: 1000,
            clearCart: mockClearCart,
        });
        localStorage.clear();
        localStorage.setItem('token', 'mock-token');
    });

    afterEach(() => {
        jest.clearAllMocks();
    });

    it('renders checkout summary correctly', async () => {
        await act(async () => {
             render(<CartPage />);
        });

        expect(screen.getByText('Paracetamol')).toBeInTheDocument();
        expect(screen.getByText('1,000')).toBeInTheDocument(); // Total Paybale
    });

    it('switches between pickup and delivery methods', async () => {
        await act(async () => {
             render(<CartPage />);
        });

        const deliveryBtn = screen.getByText(/Home Delivery/i);
        fireEvent.click(deliveryBtn);

        expect(screen.getByText(/Delivery fees must be paid/i)).toBeInTheDocument();
    });

    it('shows QR modal on Confirm & Pay click', async () => {
        await act(async () => {
             render(<CartPage />);
        });

        const confirmBtn = screen.getByText(/Confirm & Pay/i);
        fireEvent.click(confirmBtn);

        expect(screen.getByText(/Pay & Upload/i)).toBeInTheDocument();
        // Use getAllByText as the price appears both in the summary and the modal
        expect(screen.getAllByText('1,000').length).toBeGreaterThan(0);
    });

    it('handles successful order submission', async () => {
        (axios.post as jest.Mock).mockResolvedValueOnce({
            status: 201,
            data: { order_id: 'ORDER-123', invoice_number: 'INV-123' }
        });

        await act(async () => {
             render(<CartPage />);
        });

        // Trigger Modal
        fireEvent.click(screen.getByText(/Confirm & Pay/i));

        // Mock file upload
        const file = new File(['test'], 'receipt.png', { type: 'image/png' });
        const input = screen.getByLabelText(/receipt.png|Choose Photo/i);
        fireEvent.change(input, { target: { files: [file] } });

        // Click Finish/Order Confirm
        fireEvent.click(screen.getByText(/Order Confirm/i));

        await waitFor(() => {
            expect(screen.getByText(/Order Successful!/i)).toBeInTheDocument();
            expect(screen.getByText(/#ORDER-123/i)).toBeInTheDocument();
            expect(mockClearCart).toHaveBeenCalled();
        });
    });
});
