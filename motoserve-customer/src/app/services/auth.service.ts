import { inject, Injectable } from '@angular/core';
import { Preferences } from '@capacitor/preferences';
import { firstValueFrom } from 'rxjs';
import { CustomerApiService } from './customer-api.service';

export interface Customer {
  id: number;
  name: string;
  email: string;
  phone: string;
  role: string;
  status: string;
}

export interface AuthResponse {
  message: string;
  user: Customer;
  token: string;
}

export interface RegisterData {
  name: string;
  email: string;
  phone: string;
  password: string;
  password_confirmation: string;
}

@Injectable({ providedIn: 'root' })
export class AuthService {
  private api = inject(CustomerApiService);
  private readonly tokenKey = 'motoserve_customer_token';

  async login(email: string, password: string): Promise<AuthResponse> {
    const response = await firstValueFrom(
      this.api.post<AuthResponse>('login', { email, password })
    );

    await Preferences.set({
      key: this.tokenKey,
      value: response.token,
    });

    return response;
  }

  async register(data: RegisterData): Promise<AuthResponse> {
    const response = await firstValueFrom(
      this.api.post<AuthResponse>('register', data)
    );

    await Preferences.set({
      key: this.tokenKey,
      value: response.token,
    });

    return response;
  }

  async getToken(): Promise<string | null> {
    const { value } = await Preferences.get({ key: this.tokenKey });
    return value;
  }

  async clearToken(): Promise<void> {
    await Preferences.remove({ key: this.tokenKey });
  }
}
