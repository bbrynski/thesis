import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Customer } from '../shared/customer';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class CustomerService {
  private isEditMode = false;
  private toEditCustomer: Customer;
  private customerUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Klient';

  constructor(private http: HttpClient) { }

  getAllCustomer(): Observable<any[]> {
    return this.http.get<any[]>(this.customerUrl);
  }

  getOneCustomer(index: number): Observable<any> {
    return this.http.get<any[]>(this.customerUrl + '/' + index);
  }

  addCustomer(customer: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.customerUrl + '/edytuj', customer, httpOptions);
    } else {
      return this.http.post<any>(this.customerUrl + '/wstaw', customer, httpOptions);
    }
  }

  deleteCustomer(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.customerUrl + '/usun/' + index);
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditCustomer(customer: Customer) {
    this.toEditCustomer = customer;
    this.changeEditMode();
  }
  getEditCustomer() {
    return this.toEditCustomer;
  }

  updateReservationAmount(idCustomer: number) {
    return this.http.post<any>(this.customerUrl + '/rezerwacje/update', idCustomer, httpOptions);
  }
}
