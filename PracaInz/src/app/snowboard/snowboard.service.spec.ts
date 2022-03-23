import { TestBed, inject } from '@angular/core/testing';

import { SnowboardService } from './snowboard.service';

describe('SnowboardService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [SnowboardService]
    });
  });

  it('should be created', inject([SnowboardService], (service: SnowboardService) => {
    expect(service).toBeTruthy();
  }));
});
