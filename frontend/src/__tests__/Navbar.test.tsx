import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom';
import Navbar from '../components/layout/Navbar';
import { useAuth } from '../context/AuthContext';
import { useCart } from '../context/CartContext';
import { usePathname } from 'next/navigation';

// Mock the contexts and navigation
jest.mock('../context/AuthContext', () => ({
  useAuth: jest.fn(),
}));

jest.mock('../context/CartContext', () => ({
  useCart: jest.fn(),
}));

jest.mock('next/navigation', () => ({
  usePathname: jest.fn(),
}));

describe('Navbar Component', () => {
  const mockLogout = jest.fn();

  beforeEach(() => {
    (useCart as jest.Mock).mockReturnValue({ totalItems: 0 });
    (usePathname as jest.Mock).mockReturnValue('/');
  });

  afterEach(() => {
    jest.clearAllMocks();
  });

  it('renders branding logo correctly', () => {
    (useAuth as jest.Mock).mockReturnValue({ user: null, logout: mockLogout });
    render(<Navbar />);
    expect(screen.getByText(/ZweToe/i)).toBeInTheDocument();
    expect(screen.getByText(/Pharmacy/i)).toBeInTheDocument();
  });

  it('shows Sign In and Register links for guests', () => {
    (useAuth as jest.Mock).mockReturnValue({ user: null, logout: mockLogout });
    render(<Navbar />);
    expect(screen.getByText(/Sign In/i)).toBeInTheDocument();
    expect(screen.getByText(/Register/i)).toBeInTheDocument();
  });

  it('shows user profile and Logout for authenticated members', () => {
    (useAuth as jest.Mock).mockReturnValue({ 
      user: { name: 'John Doe', role: 'customer' }, 
      logout: mockLogout 
    });
    render(<Navbar />);
    expect(screen.getByText(/John Doe/i)).toBeInTheDocument();
    expect(screen.getByText(/Logout/i)).toBeInTheDocument();
  });

  it('triggers logout function when logout button is clicked', () => {
    (useAuth as jest.Mock).mockReturnValue({ 
      user: { name: 'John Doe', role: 'customer' }, 
      logout: mockLogout 
    });
    render(<Navbar />);
    const logoutBtn = screen.getByText(/Logout/i);
    fireEvent.click(logoutBtn);
    expect(mockLogout).toHaveBeenCalledTimes(1);
  });

  it('displays correct number of items in cart badge', () => {
    (useAuth as jest.Mock).mockReturnValue({ 
      user: { name: 'John Doe', role: 'customer' }, 
      logout: mockLogout 
    });
    (useCart as jest.Mock).mockReturnValue({ totalItems: 5 });
    
    render(<Navbar />);
    expect(screen.getByText('5')).toBeInTheDocument();
  });
});
