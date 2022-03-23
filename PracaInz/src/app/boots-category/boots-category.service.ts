import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { BootsCategory } from '../shared/boots-category';

@Injectable({
  providedIn: 'root'
})
export class BootsCategoryService {

  private urlBootsCategory = 'http://localhost/repository/pracainz/PracaInzBackend/ButyKategoria';

  constructor(private http: HttpClient) { }

  getAllBootsCategory(): Observable<BootsCategory[]> {
    return this.http.get<BootsCategory[]>(this.urlBootsCategory);
  }
}
