import http from 'k6/http';
import { sleep, check } from 'k6';

export let options = {
  vus: 50,
  duration: '30s',
  thresholds: {
    http_req_duration: ['p(95)<500'],
    http_req_failed: ['rate<0.01'],
  },
};

const BASE_URL = __ENV.TARGET_URL || 'http://127.0.0.1:8000';

export default function () {
  const res = http.get(`${BASE_URL}/api/assets`);

  check(res, {
    'status is 200': (r) => r.status === 200,
  });

  sleep(1);
}
