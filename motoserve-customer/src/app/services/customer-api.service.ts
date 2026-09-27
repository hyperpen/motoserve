import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({ providedIn: 'root' })
export class CustomerApiService {
  private http = inject(HttpClient);
  private readonly baseUrl = 'http://127.0.0.1:8000/api/customer';

  assetUrl(path: string): string {
    if (/^https?:\/\//i.test(path)) return path;
    const clean = path.replace(/^\/+/, '');
    return new URL('/' + (clean.startsWith('storage/') ? clean : 'storage/' + clean), this.baseUrl).toString();
  }

  get<T>(path: string) {
    return this.http.get<T>(`${this.baseUrl}/${path}`);
  }

  post<T>(path: string, body: unknown) {
    return this.http.post<T>(`${this.baseUrl}/${path}`, body);
  }

  patch<T>(path: string, body: unknown) {
  return this.http.patch<T>(`${this.baseUrl}/${path}`, body);
  }

  put<T>(path: string, body: unknown) {
    return this.http.put<T>(`${this.baseUrl}/${path}`, body);
  }

  delete<T>(path: string) {
    return this.http.delete<T>(`${this.baseUrl}/${path}`);
  }
}