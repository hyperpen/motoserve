import { Component, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpErrorResponse } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import {
  IonButton,
  IonCard,
  IonCardContent,
  IonCardHeader,
  IonCardTitle,
  IonContent,
  IonHeader,
  IonModal,
  IonSelect,
  IonSelectOption,
  IonText,
  IonTextarea,
  IonTitle,
  IonToolbar,
} from '@ionic/angular';

import { CustomerApiService } from '../services/customer-api.service';
import { ConfirmDialogService } from '../services/confirm-dialog.service';

interface Motorcycle {
  id: number;
  brand: string;
  model: string;
  plate_number: string | null;
}

interface Service {
  id: number;
  name: string;
}

interface Mechanic {
  id: number;
  name: string;
}

interface Appointment {
  id: number;
  appointment_number: string;
  appointment_date: string;
  appointment_time: string;
  status: string;
  problem_description: string | null;
  motorcycle: Motorcycle | null;
  service: Service | null;
  mechanic: Mechanic | null;
}

@Component({
  selector: 'app-tab3',
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
    IonModal,
    IonSelect,
    IonSelectOption,
    IonText,
    IonTextarea,
    IonTitle,
    IonToolbar,
  ],
  template: `
    <ion-header><ion-toolbar><ion-title>Appointments</ion-title></ion-toolbar></ion-header>
    <ion-content><main class="ms-shell ms-shell--narrow">
      <div class="ms-intro"><div><h1>Appointments</h1><p>Book and track service visits.</p></div><ion-button size="small" (click)="openBooking()">+ Book</ion-button></div>
      @if (successMessage()) { <ion-text color="success"><p class="ms-feedback">{{ successMessage() }}</p></ion-text> }
      @if (errorMessage() && !bookingOpen()) { <ion-text color="danger"><p class="ms-feedback">{{ errorMessage() }}</p></ion-text> }
      @if (loading()) { <p class="ms-muted">Loading appointments...</p> }
      @else if (appointments().length === 0) { <div class="ms-empty">No appointments yet.</div> }
      @else { @for (appointment of appointments(); track appointment.id) {
        <ion-card class="booking-card"><ion-card-header><ion-card-title>{{ appointment.appointment_number }}</ion-card-title></ion-card-header><ion-card-content>
          <p>Service: {{ appointment.service?.name || 'Unavailable' }}</p>
          <p>Motorcycle: {{ appointment.motorcycle?.brand || '' }} {{ appointment.motorcycle?.model || 'Unavailable' }}</p>
          <p>{{ appointment.appointment_date.slice(0, 10) }} · {{ appointment.appointment_time.slice(0, 5) }}</p><p>Status: {{ appointment.status }}</p>
          @if (appointment.mechanic) { <p>Mechanic: {{ appointment.mechanic.name }}</p> }
          @if (appointment.problem_description) { <p>Problem: {{ appointment.problem_description }}</p> }
          @if (canEdit(appointment)) { <ion-button size="small" fill="outline" [disabled]="saving()" (click)="openEdit(appointment)">Edit</ion-button> }
          @if (canCancel(appointment)) { <ion-button size="small" color="danger" fill="outline" [disabled]="cancellingId() === appointment.id" (click)="cancel(appointment)">{{ cancellingId() === appointment.id ? 'Cancelling...' : 'Cancel Appointment' }}</ion-button> }
        </ion-card-content></ion-card>
      } }
    </main></ion-content>
    <ion-modal class="ms-dialog" [isOpen]="bookingOpen()" (didDismiss)="closeBooking()"><ng-template>
      <ion-header><ion-toolbar><ion-title>{{ editingId() === null ? 'Book a Service' : 'Edit Appointment' }}</ion-title><button class="ms-close" slot="end" type="button" aria-label="Close" (click)="closeBooking()">×</button></ion-toolbar></ion-header>
      <ion-content class="ion-padding">      @if (loading()) {
        <p>Loading booking options...</p>
      } @else {
        @if (motorcycles().length === 0) {
          <p>Add a motorcycle before booking an appointment.</p>
          <ion-button size="small" href="/tabs/tab2">
            Go to Motorcycles
          </ion-button>
        } @else if (services().length === 0) {
          <p>No services are available right now.</p>
        } @else {
          <form class="ms-form" (ngSubmit)="book()">
            <ion-select
              label="Motorcycle"
              labelPlacement="stacked"
              fill="outline"
              placeholder="Select motorcycle"
              name="motorcycle_id"
              [(ngModel)]="motorcycleId">
              @for (motorcycle of motorcycles(); track motorcycle.id) {
                <ion-select-option [value]="motorcycle.id">
                  {{ motorcycle.brand }} {{ motorcycle.model }}
                </ion-select-option>
              }
            </ion-select>

            <ion-select
              label="Service"
              labelPlacement="stacked"
              fill="outline"
              placeholder="Select service"
              name="service_id"
              [(ngModel)]="serviceId">
              @for (service of services(); track service.id) {
                <ion-select-option [value]="service.id">
                  {{ service.name }}
                </ion-select-option>
              }
            </ion-select>

            <label class="field-label" for="appointment-date">
              Appointment Date
            </label>
            <input
              id="appointment-date"
              class="date-time-input"
              type="date"
              name="appointment_date"
              [min]="today"
              [(ngModel)]="appointmentDate"
              required>

            <label class="field-label" for="appointment-time">
              Appointment Time
            </label>
            <input
              id="appointment-time"
              class="date-time-input"
              type="time"
              name="appointment_time"
              [(ngModel)]="appointmentTime"
              required>

            <ion-textarea
              label="Problem Description (optional)"
              labelPlacement="stacked"
              fill="outline"
              name="problem_description"
              [(ngModel)]="problemDescription"
              maxlength="2000"
              [autoGrow]="true">
            </ion-textarea>

            @if (errorMessage()) { <ion-text color="danger"><p>{{ errorMessage() }}</p></ion-text> }
            <ion-button
              size="small"
              type="submit"
              [disabled]="saving()">
              {{ saving() ? 'Saving...' : editingId() === null ? 'Book Appointment' : 'Save Changes' }}
            </ion-button>
          </form>
        }
      }

</ion-content>
    </ng-template></ion-modal>
  `,
  styles: [`
    ion-select,
    ion-textarea {
      margin: 14px 0;
    }

    .field-label {
      display: block;
      margin-top: 16px;
      margin-bottom: 6px;
    }

    .date-time-input {
      display: block;
      box-sizing: border-box;
      width: 100%;
      padding: 14px;
      color: inherit;
      background: transparent;
      border: 1px solid #777;
      border-radius: 5px;
      font: inherit;
    }

    .booking-card { margin: 12px 0; }
    .booking-card p { margin: 6px 0; }
  `],
})
export class Tab3Page implements OnInit {
  private api = inject(CustomerApiService);
  private confirmDialog = inject(ConfirmDialogService);

  motorcycles = signal<Motorcycle[]>([]);
  services = signal<Service[]>([]);
  appointments = signal<Appointment[]>([]);

  loading = signal(true);
  saving = signal(false);
  bookingOpen = signal(false);
  editingId = signal<number | null>(null);
  cancellingId = signal<number | null>(null);
  successMessage = signal('');
  errorMessage = signal('');

  motorcycleId: number | null = null;
  serviceId: number | null = null;
  appointmentDate = '';
  appointmentTime = '';
  problemDescription = '';

  today = this.localDate();

  ngOnInit() {
    void this.loadData();
  }

  async openBooking() {
    this.editingId.set(null);
    this.motorcycleId = null;
    this.serviceId = null;
    this.appointmentDate = '';
    this.appointmentTime = '';
    this.problemDescription = '';
    this.errorMessage.set('');
    this.bookingOpen.set(true);
    await this.loadData();
  }

  openEdit(appointment: Appointment) {
    if (!this.canEdit(appointment)) return;
    this.editingId.set(appointment.id);
    this.motorcycleId = appointment.motorcycle?.id ?? null;
    this.serviceId = appointment.service?.id ?? null;
    this.appointmentDate = appointment.appointment_date.slice(0, 10);
    this.appointmentTime = appointment.appointment_time.slice(0, 5);
    this.problemDescription = appointment.problem_description ?? '';
    this.errorMessage.set('');
    this.bookingOpen.set(true);
  }

  closeBooking() {
    this.bookingOpen.set(false);
    this.editingId.set(null);
  }

  canEdit(appointment: Appointment): boolean {
    return ['pending', 'confirmed'].includes(appointment.status);
  }

  async loadData() {
    this.loading.set(true);
    this.errorMessage.set('');

    try {
      const [motorcycles, services, appointments] =
        await Promise.all([
          firstValueFrom(
            this.api.get<{ motorcycles: Motorcycle[] }>(
              'motorcycles'
            )
          ),
          firstValueFrom(
            this.api.get<{ services: Service[] }>('services')
          ),
          firstValueFrom(
            this.api.get<{ appointments: Appointment[] }>(
              'appointments'
            )
          ),
        ]);

      this.motorcycles.set(motorcycles.motorcycles);
      this.services.set(services.services);
      this.appointments.set(appointments.appointments);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.loading.set(false);
    }
  }

  async book() {
    if (this.saving()) return;

    if (
      this.motorcycleId === null ||
      this.serviceId === null ||
      !this.appointmentDate ||
      !this.appointmentTime
    ) {
      this.errorMessage.set(
        'Select a motorcycle, service, date, and time.'
      );
      return;
    }

    this.saving.set(true);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const editingId = this.editingId();
      const body = {
        motorcycle_id: this.motorcycleId,
        service_id: this.serviceId,
        appointment_date: this.appointmentDate,
        appointment_time: this.appointmentTime,
        problem_description: this.problemDescription.trim() || null,
      };
      const response = await firstValueFrom(
        editingId === null
          ? this.api.post<{ message: string; appointment: Appointment }>('appointments', body)
          : this.api.put<{ message: string; appointment: Appointment }>(`appointments/${editingId}`, body)
      );

      this.appointmentDate = '';
      this.appointmentTime = '';
      this.problemDescription = '';

      await this.loadData();
      this.successMessage.set(response.message);
      this.closeBooking();
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.saving.set(false);
    }
  }

  canCancel(appointment: Appointment): boolean {
    return [
      'pending',
      'confirmed',
      'mechanic_assigned',
    ].includes(appointment.status);
  }

  async cancel(appointment: Appointment) {
    if (this.cancellingId() !== null) return;

    const confirmed = await this.confirmDialog.confirm({
      header: 'Cancel appointment?',
      message: `Cancel appointment ${appointment.appointment_number}?`,
      cancelText: 'Keep Booking',
      confirmText: 'Cancel Appointment',
    });

    if (!confirmed) return;

    this.cancellingId.set(appointment.id);
    this.errorMessage.set('');
    this.successMessage.set('');

    try {
      const response = await firstValueFrom(
        this.api.patch<{ message: string }>(
          `appointments/${appointment.id}/cancel`,
          {}
        )
      );

      await this.loadData();
      this.successMessage.set(response.message);
    } catch (error) {
      this.errorMessage.set(this.describeError(error));
    } finally {
      this.cancellingId.set(null);
    }
  }

  private localDate(): string {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
  }

  private describeError(error: unknown): string {
    if (error instanceof HttpErrorResponse) {
      if (error.status === 0) {
        return 'Cannot connect to MotoServe API.';
      }

      const errors = error.error?.errors as
        Record<string, string[]> | undefined;

      return (
        (errors ? Object.values(errors)[0]?.[0] : undefined) ??
        error.error?.message ??
        `API error (${error.status}).`
      );
    }

    return error instanceof Error
      ? error.message
      : 'Something went wrong.';
  }
}