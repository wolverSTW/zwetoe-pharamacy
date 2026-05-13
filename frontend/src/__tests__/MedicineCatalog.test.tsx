import React from 'react';
import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import '@testing-library/jest-dom';
import MedicinesPage from '../app/products/page';
import { medicineService } from '../services/medicineService';
import { categoryService } from '../services/categoryService';
import { useCart } from '../context/CartContext';

jest.mock('../services/medicineService', () => ({
  medicineService: {
    getAll: jest.fn(),
  },
}));

jest.mock('../services/categoryService', () => ({
  categoryService: {
    getAll: jest.fn(),
  },
}));

jest.mock('../context/CartContext', () => ({
  useCart: jest.fn(),
}));

describe('MedicineCatalog - MedicinesPage', () => {
  const mockMedicines = [
    { id: 1, name: 'Amoxicillin', sell_price: 2000, category: { name: 'Antibiotics' }, image: 'amo.jpg', stock_quantity: 10 },
    { id: 2, name: 'Paracetamol', sell_price: 500, category: { name: 'Painkillers' }, image: 'para.jpg', stock_quantity: 50 },
    { id: 3, name: 'Cough Syrup', sell_price: 1500, category: { name: 'Flu' }, image: 'cough.jpg', stock_quantity: 5 },
  ];

  const mockCategories = [
    { id: 1, name: 'Antibiotics' },
    { id: 2, name: 'Painkillers' },
    { id: 3, name: 'Flu' },
  ];

  beforeEach(() => {
    (medicineService.getAll as jest.Mock).mockResolvedValue(mockMedicines);
    (categoryService.getAll as jest.Mock).mockResolvedValue(mockCategories);
    (useCart as jest.Mock).mockReturnValue({ addToCart: jest.fn() });
    localStorage.clear();
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  it('renders medicines and the category control after fetching', async () => {
    await act(async () => {
      render(<MedicinesPage />);
    });

    await waitFor(() => {
      expect(screen.getByText('Amoxicillin')).toBeInTheDocument();
      expect(screen.getByText('Paracetamol')).toBeInTheDocument();
      expect(screen.getByText('All Categories')).toBeInTheDocument();
    });
  });

  it('filters medicines by search term', async () => {
    await act(async () => {
      render(<MedicinesPage />);
    });

    const searchInput = screen.getByPlaceholderText(/Search catalogue/i);
    fireEvent.change(searchInput, { target: { value: 'Amox' } });

    await waitFor(() => {
      expect(screen.getByText('Amoxicillin')).toBeInTheDocument();
      expect(screen.queryByText('Paracetamol')).not.toBeInTheDocument();
    });
  });

  it('filters medicines by category selection', async () => {
    await act(async () => {
      render(<MedicinesPage />);
    });

    fireEvent.click(screen.getByRole('button', { name: /All Categories/i }));
    fireEvent.click(await screen.findByRole('button', { name: /Flu/i }));

    await waitFor(() => {
      expect(screen.getByText('Cough Syrup')).toBeInTheDocument();
      expect(screen.queryByText('Amoxicillin')).not.toBeInTheDocument();
    });
  });

  it('displays private pricing lock for guest users', async () => {
    await act(async () => {
      render(<MedicinesPage />);
    });

    await waitFor(() => {
      expect(screen.getAllByText(/Private/i).length).toBeGreaterThan(0);
      expect(screen.queryByText(/^MMK$/i)).not.toBeInTheDocument();
    });
  });

  it('displays actual prices for logged in users', async () => {
    localStorage.setItem('user', JSON.stringify({ name: 'John Doe', role: 'customer' }));

    await act(async () => {
      render(<MedicinesPage />);
    });

    await waitFor(() => {
      expect(screen.getByText('2,000')).toBeInTheDocument();
      expect(screen.getAllByText('MMK').length).toBeGreaterThan(0);
    });
  });
});
