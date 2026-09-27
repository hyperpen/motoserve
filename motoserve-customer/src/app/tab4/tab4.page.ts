import {
  ChangeDetectorRef,
  Component,
  OnInit,
  inject,
  signal,
} from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
import { firstValueFrom } from 'rxjs';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardTitle,
  IonContent,
  IonHeader,
  IonInput,
  IonSelect,
  IonSelectOption,
  IonText,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { CustomerApiService } from '../services/customer-api.service';
import { CartService } from '../services/cart.service';

interface Category {
  id: number;
  name: string;
}

interface Product {
  id: number;
  sku: string;
  name: string;
  brand: string | null;
  description: string | null;
  compatible_models: string | null;
  price: string;
  stock: number;
  image: string | null;
  category: Category | null;
}

interface ProductPage {
  data: Product[];
  current_page: number;
  last_page: number;
}

@Component({
  selector: 'app-tab4',
  standalone: true,
  imports: [
    FormsModule,
    IonButton,
    IonCard,
    IonCardContent,
    IonCardHeader,
    IonCardTitle,
    IonContent,
    IonHeader,
    IonInput,
    IonSelect,
    IonSelectOption,
    IonText,
    IonTitle,
    IonToolbar,
  ],
  template: `
    <ion-header>
      <ion-toolbar>
        <ion-title>Parts Shop</ion-title>
      </ion-toolbar>
    </ion-header>

    <ion-content><main class="ms-shell">
      <div class="ms-intro"><div><h1>Parts Shop</h1><p>Find the right parts for your ride.</p></div></div>
      <form class="shop-filters" (ngSubmit)="loadProducts(1)">
        <ion-input
          label="Search parts"
          labelPlacement="stacked"
          fill="outline"
          name="search"
          [(ngModel)]="search"
          placeholder="Name, brand, or compatible model">
        </ion-input>

        <ion-select
          label="Category"
          labelPlacement="stacked"
          fill="outline"
          name="category"
          [(ngModel)]="categoryId">
          <ion-select-option [value]="null">
            All Categories
          </ion-select-option>

          @for (category of categories(); track category.id) {
            <ion-select-option [value]="category.id">
              {{ category.name }}
            </ion-select-option>
          }
        </ion-select>

        <ion-button size="small" type="submit">
          Search Products
        </ion-button>
      </form>

      <ion-button
        size="small"
        fill="outline"
        (click)="openCart()">
        View Cart ({{ cart.count() }})
      </ion-button>

      <ion-button
  size="small"
  fill="clear"
  (click)="openOrders()">
  My Pickup Orders
</ion-button>

      @if (errorMessage()) {
        <ion-text color="danger">
          <p>{{ errorMessage() }}</p>
        </ion-text>
      }

      @if (cartMessage()) {
        <ion-text color="success">
          <p>{{ cartMessage() }}</p>
        </ion-text>
      }

      @if (loading()) {
        <p>Loading products...</p>
      } @else if (products().length === 0) {
        <p class="ms-empty">No products found.</p>
      } @else {
        <div class="product-grid">
        @for (product of products(); track product.id) {
          <ion-card class="product-card">
            <div class="product-media">
              @if (product.image) { <img [src]="api.assetUrl(product.image)" [alt]="product.name" loading="lazy"> }
              @else { <span class="product-placeholder" aria-hidden="true">M</span> }
            </div>
            <ion-card-header>
              <small>{{ product.category?.name || 'Motorcycle Part' }}</small>
              <ion-card-title>{{ product.name }}</ion-card-title>
            </ion-card-header>

            <ion-card-content>
              @if (product.brand) {
                <p>Brand: {{ product.brand }}</p>
              }

              @if (product.category) {
                <p>Category: {{ product.category.name }}</p>
              }

              @if (product.compatible_models) {
                <p>
                  Compatible with: {{ product.compatible_models }}
                </p>
              }

              @if (product.description) {
                <p>{{ product.description }}</p>
              }

<p class="price">{{ formatPrice(product.price) }}</p>
              <p>{{ product.stock }} in stock</p>

              <ion-button
                size="small"
                type="button"
                [disabled]="product.stock < 1"
                (click)="addToCart(product)">
                Add to Cart
              </ion-button>
            </ion-card-content>
          </ion-card>
        }

        </div>
        <div class="pagination">
          <ion-button
            fill="outline"
            [disabled]="page() <= 1 || loading()"
            (click)="loadProducts(page() - 1)">
            Previous
          </ion-button>

          <span>Page {{ page() }} of {{ lastPage() }}</span>

          <ion-button
            fill="outline"
            [disabled]="page() >= lastPage() || loading()"
            (click)="loadProducts(page() + 1)">
            Next
          </ion-button>
        </div>
      }
    </main></ion-content>
  `,
  styles: [`
    .shop-filters { display: grid; grid-template-columns: minmax(0,2fr) minmax(140px,1fr) auto; gap: 8px; align-items: end; margin-bottom: 14px; }
    .shop-filters ion-button { margin: 0; }
    .product-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(190px,1fr)); gap: 12px; }
    .product-card { margin: 0; overflow: hidden; }
    .product-media { height: 155px; display: grid; place-items: center; background: linear-gradient(145deg,#28354b,#182335); }
    .product-media img { width: 100%; height: 100%; object-fit: cover; }
    .product-placeholder { width: 65px; height: 65px; display: grid; place-items: center; border-radius: 18px; background: linear-gradient(135deg,#607ff4,#896dec); color: white; font-size: 38px; font-weight: 900; }
    .product-card ion-card-header { padding: 12px 12px 0; }
    .product-card ion-card-title { font-size: 15px; line-height: 1.25; }
    .product-card ion-card-content { padding: 10px 12px 13px; }
    .product-card p { margin: 5px 0; font-size: 11px; }
    .product-card .price { font-size: 16px; font-weight: 800; }
    @media (max-width: 520px) { .shop-filters { grid-template-columns: minmax(0,1fr) 110px; } .shop-filters ion-input { grid-column: 1 / -1; } .product-grid { grid-template-columns: repeat(2,minmax(0,1fr)); gap: 9px; } .product-media { height: 125px; } }
    ion-input,
    ion-select {
      margin: 14px 0;
    }

    ion-card {
      margin: 16px 0;
    }

    ion-card ion-button {
      margin-top: 16px;
    }

    .pagination {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin: 20px 0;
    }
  `],
})
export class Tab4Page implements OnInit {
  api = inject(CustomerApiService);
  private router = inject(Router);
  private cdr = inject(ChangeDetectorRef);

  cart = inject(CartService);

  categories = signal<Category[]>([]);
  products = signal<Product[]>([]);
  page = signal(1);
  lastPage = signal(1);
  loading = signal(true);
  errorMessage = signal('');
  cartMessage = signal('');

  search = '';
  categoryId: number | null = null;

  ngOnInit(): void {
    void this.loadCategories();
    void this.loadProducts(1);
  }

  openCart(): void {
    void this.router.navigateByUrl('/cart');
  }

  openOrders(): void {
  void this.router.navigateByUrl('/orders');
}

  async loadCategories(): Promise<void> {
    try {
      const response = await firstValueFrom(
        this.api.get<{ categories: Category[] }>(
          'shop/categories'
        )
      );

      this.categories.set(response.categories);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.cdr.markForCheck();
    }
  }

  async loadProducts(requestedPage: number): Promise<void> {
    this.loading.set(true);
    this.errorMessage.set('');
    this.cartMessage.set('');

    const params = new URLSearchParams();
    params.set('page', String(requestedPage));

    if (this.categoryId !== null) {
      params.set('category_id', String(this.categoryId));
    }

    if (this.search.trim()) {
      params.set('search', this.search.trim());
    }

    try {
      const response = await firstValueFrom(
        this.api.get<ProductPage>(
          `shop/products?${params.toString()}`
        )
      );

      this.products.set(response.data);
      this.page.set(response.current_page);
      this.lastPage.set(response.last_page);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.loading.set(false);
      this.cdr.markForCheck();
    }
  }

  async addToCart(product: Product): Promise<void> {
    this.errorMessage.set('');
    this.cartMessage.set('');

    try {
      await this.cart.add(product);

      this.cartMessage.set(
        `${product.name} added to cart. Items: ${this.cart.count()}`
      );
    } catch (error) {
      this.errorMessage.set(
        error instanceof Error
          ? error.message
          : 'Could not add this product to cart.'
      );
    } finally {
      this.cdr.markForCheck();
    }
  }

  formatPrice(price: string): string {
    return Number(price).toLocaleString('en-PH', {
      style: 'currency',
      currency: 'PHP',
    });
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      return error.status === 0
        ? 'Cannot connect to MotoServe API.'
        : error.error?.message ?? `API error (${error.status}).`;
    }

    return 'Cannot load shop products.';
  }
}